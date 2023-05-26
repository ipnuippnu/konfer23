<?php

namespace App\Jobs;

use App\Models\Participant;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParticipantCardGeneratorJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Queueable, Dispatchable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Participant $participant
    )
    {
        $this->onQueue('generator.id_card');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            // Determine if the batch has been cancelled...
 
            return;
        }

        $qr = Storage::path("temp/{$this->participant->id}.png");

        QrCode::style('round')
            ->format('png')
            ->size(800)
            ->color(51,41,75)
            ->eyeColor(0, 148, 28, 138, 20, 127, 74)
            ->eyeColor(1, 148, 28, 138, 20, 127, 74)
            ->eyeColor(2, 148, 28, 138, 20, 127, 74)
            ->generate($this->participant->code->id, $qr);

        $template = \Image::make(resource_path('templates/idcard-peserta.jpg'));
        
        $template->text($this->participant->limit_name, 620, 554, function($font){
            $font->file(resource_path('templates/fonts/regular.ttf'));
            $font->size(80);
            $font->color('#fff');
            $font->align('center');
            $font->valign('top');
        });

        $template->text($this->participant->delegator->name, 620, 680, function($font){
            $font->file(resource_path('templates/fonts/bold.ttf'));
            $font->size(40);
            $font->color('#33294b');
            $font->align('center');
            $font->valign('top');
        });

        $template->text($this->participant->delegator->address_code . " ({$this->participant->delegator->payment->code->id})", 1040,1807, function($font){
            $font->file(resource_path('templates/fonts/bold.ttf'));
            $font->size(50);
            $font->color('#fff');
            $font->align('right');
            $font->valign('top');
        });

        $template->insert(\Image::make($qr)->resize(580, 580), 'center', 0, 363);

        $filename = config('konfer.kecamatan')[substr($this->participant->delegator->address_code, 0, 8)] . "/{$this->participant->delegator->payment->code->id}/{$this->participant->delegator->name} - {$this->participant->name}.jpg";

        Storage::disk('participants_card')->put($filename, $template->encode('jpg', 80));
        $this->participant->update([
            'card' => $filename
        ]);

        $template->destroy();
        unlink($qr);
    }

}
