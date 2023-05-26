<?php

namespace App\Listeners;

use App\Events\UpdateAllIdCardEvent;
use App\Jobs\ParticipantCardGeneratorJob;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Files\Disk;
use ZipArchive;

class UpdateAllIdCardListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(UpdateAllIdCardEvent $event): void
    {

        $lock = Cache::lock('id_card_massal');

        if($lock->get())
        {

            if($event->from == null) $event->from = Carbon::parse('2001-07-08');
            if($event->to == null) $event->to = Carbon::now();

            $files = Storage::disk('participants_card')->allFiles();
            foreach ($files as $file) {
                if ($file !== '.gitignore' && !preg_match("/^\.archived\//", $file)) {
                    Storage::disk('participants_card')->delete($file);
                }
            }
    
            $folders = Storage::disk('participants_card')->allDirectories();
            foreach ($folders as $folder) {
                if(!preg_match("/^\.archived\/?/", $folder))
                {
                    Storage::disk('participants_card')->deleteDirectory($folder);
                }
            }
    
            $collection = Delegator::with('code', 'payment')->join('delegator_steps as s', 'delegators.id', '=', 's.delegator_id')->select('delegators.id', 'delegators.name', 's.created_at')->where('s.step', DelegatorStep::$LUNAS)->orderBy('s.created_at', 'ASC')->whereBetween('s.created_at', [$event->from, $event->to])->get()->reduce(function($carry, $val){
    
                return $carry->merge($val->participants);
    
            }, collect());
    
            $jobs = collect();

            $collection->each(function(Participant $participant) use($jobs) {
                $jobs->push(new ParticipantCardGeneratorJob($participant));
            });
    
            Bus::batch($jobs->toArray())->then(function(){

                $disk = Storage::disk('participants_card');
                $zipName = '.archived/(IDPeserta) ' . Carbon::now()->format('Y-m-i H.i.s') . 'WIB.zip';
                
                $zip = new ZipArchive();
                $zip->open($disk->path($zipName), ZipArchive::CREATE | ZipArchive::OVERWRITE);

                $files = $disk->allFiles();
                foreach($files as $file)
                {
                    if(!preg_match("/^(\.)/", $file))
                    {
                        $relativePath = $file;
                        $fileContent = $disk->get($file);
                        $zip->addFromString($relativePath, $fileContent);
                    }
                }

                unset($fileContent);

                $zip->close();

                activity('generate')->log('ID Card Massal Selesai');

            })->catch(function($b, $t){
                activity('generate')->log('ID Card Massal Gagal');
            })->finally(
                fn() => $lock->release()
            )->onQueue('generator.id_card')->dispatch();
    
            activity('generate')->log('ID Card Massal Diproses');

        }
        else
        {
            abort(403, 'ID Card sedang diproses');
        }
    }

}
