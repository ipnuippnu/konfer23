<?php

namespace App\Jobs;

use App\Models\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as ImageImage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvitationGeneratorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Guest $guest
    )
    {
        $this->onQueue('generator.invitation');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $luar = Image::make(resource_path('templates/undangan-depan.png'));

        $qr = Image::make(base64_encode(QrCode::style('round')
            ->format('png')
            ->size(800)
            ->color(51,41,75)
            ->eyeColor(0, 148, 28, 138, 20, 127, 74)
            ->eyeColor(1, 148, 28, 138, 20, 127, 74)
            ->eyeColor(2, 148, 28, 138, 20, 127, 74)
            ->generate($this->guest->code->id)))->resize(700, 700);

        $luar->insert($qr, 'center', 1310, -220);


        function addTextBox(ImageImage &$image, $text, $positionx, $positiony, $maxCharPerLine)
        {
            $splits = explode(' ', $text);
            $currentLine = '';
            $lines = 1;

            $atas = -85;

            $text_jadi = "Yth.\n";

            foreach($splits as $word)
            {
                $currentLine .= " $word";
                if(strlen(trim($currentLine)) > $maxCharPerLine)
                {
                    $text_jadi .= substr(trim($currentLine), 0, (-1 + strlen($word) * -1)) . "\n";
                    $lines++;
                    $currentLine = $word;

                    $atas -= 85;
                }
            }

            $text_jadi .= $currentLine;
            $atas -= 85;

            $image->text($text_jadi, $positionx, ($positiony + $atas), function($font){
                $font->file(resource_path('templates/fonts/bold.ttf'));
                $font->size(80);
                $font->color('#d7b033');
                $font->align('center');
                $font->valign('middle');
            });
        }

        addTextBox($luar, $this->guest->name, 3190, 2300, 25);

        $filename =  "{$this->guest->code->id} : {$this->guest->name}.jpg";

        Storage::disk('undangan')->put($filename, $luar->encode('jpg', 80));
        $luar->destroy();

        $this->guest->update([
            'invitation' => $filename
        ]);
    }
}
