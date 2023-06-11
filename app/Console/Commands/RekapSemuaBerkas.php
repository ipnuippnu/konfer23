<?php

namespace App\Console\Commands;

use App\Models\Delegator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class RekapSemuaBerkas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rekap Semuanya';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //Surat Pengesahan
        $this->output->info("Proses Surat Pengesahan...");
        $bar = $this->output->createProgressBar(Delegator::count());

        $zip = new ZipArchive();
        $zip->open(Storage::disk('public')->path('Surat Pengesahan.zip'), ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach(Delegator::all() as $delegator)
        {
            $bar->advance();

            $kecamatan = config('konfer.kecamatan')[substr($delegator->address_code, 0, 8)];
            $filename = "$delegator->name." . pathinfo($delegator->getAttributes()['surat_pengesahan'], PATHINFO_EXTENSION);

            try {
                $zip->addFromString("$kecamatan/$delegator->banom/$filename", Storage::disk('surat_pengesahan')->get($delegator->getAttributes()['surat_pengesahan']));
            } catch (\Throwable $th) {
                
            }
        }
        
        $zip->close();
        $bar->finish();
        $bar->clear();
        $this->output->success("Surat Pengesahan");


        //Surat Tugas
        $this->output->info("Proses Surat Tugas...");
        $bar = $this->output->createProgressBar(Delegator::count());

        $zip = new ZipArchive();
        $zip->open(Storage::disk('public')->path('Surat Tugas.zip'), ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach(Delegator::all() as $delegator)
        {
            $bar->advance();

            $kecamatan = config('konfer.kecamatan')[substr($delegator->address_code, 0, 8)];
            $filename = "$delegator->name." . pathinfo($delegator->getAttributes()['surat_tugas'], PATHINFO_EXTENSION);

            try {
                $zip->addFromString("$kecamatan/$delegator->banom/$filename", Storage::disk('surat_tugas')->get($delegator->getAttributes()['surat_tugas']));
            } catch (\Throwable $th) {
                
            }
        }
        
        $zip->close();
        $bar->finish();
        $bar->clear();
        $this->output->success("Surat Tugas");


        //Bukti Pembayaran
        $this->output->info("Proses Bukti Pembayaran...");
        $bar = $this->output->createProgressBar(Delegator::count());

        $zip = new ZipArchive();
        $zip->open(Storage::disk('public')->path('Bukti Transfer.zip'), ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach(Delegator::with('payment')->whereHas('payment')->get() as $delegator)
        {
            $bar->advance();

            $kecamatan = config('konfer.kecamatan')[substr($delegator->address_code, 0, 8)];
            $filename = "$delegator->name." . pathinfo($delegator->payment->getAttributes()['bukti_transfer'], PATHINFO_EXTENSION);

            try {
                $zip->addFromString("$kecamatan/$delegator->banom/$filename", Storage::disk('bukti_transfer')->get($delegator->payment->getAttributes()['bukti_transfer']));
            } catch (\Throwable $th) {
                
            }
        }
        
        $zip->close();
        $bar->finish();
        $bar->clear();
        $this->output->success("Bukti Transfer");

    }
}
