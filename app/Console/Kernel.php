<?php

namespace App\Console;

use App\Generators\QrCheckIn;
use App\Jobs\SendWhatsappJob;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Bus;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function(){

            $pesan = <<<EOL
            💚  💚  💚
            Assalamu'alaikum rekan/ita

            Nggak kerasa ya udah tanggal 30 Mei, 3 hari lagi nih nggak sabar pengen ketemu sama rekan/ita 🤭

            Oh iya, diatas telah kami kirimkan Code QR untuk pengambilan fasilitas KONFERCAB di tempat Check-In.

            Jangan lupa untuk hadir tepat waktu ya, See you on 2 Juni 2023. 👋👋

            ⚠️ Jangan sebarin QR Code nya ya, takutnya nanti identitas kamu disalahgunain sama orang lain 😨
            EOL;

            Delegator::whereHas('steps', fn($q) => $q->where('step', DelegatorStep::$LUNAS))->get()->groupBy('whatsapp')->each(function(Collection $data, $phone) use($pesan) {
                if($data->count() > 1)
                {
                    $jobs = $data->map(function(Delegator $delegator) use($phone) {
                        $img = base64_encode(QrCheckIn::generate($delegator, 'jpg')->getEncoded());
                        return new SendWhatsappJob($phone, "", $img);

                    })->merge([new SendWhatsappJob($phone, $pesan)])->toArray();

                    Bus::batch($jobs)->onQueue('send_wa')->dispatch();
                }
                else
                {
                    $img = base64_encode(QrCheckIn::generate($data[0], 'jpg')->getEncoded());
                    SendWhatsappJob::dispatch($phone, $pesan, $img);
                }
            });

        })->when(function(){
            return Carbon::now()->format('Y-m-d H:i') == Carbon::parse('2023-05-30 22:00')->format('Y-m-d H:i');
        });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
