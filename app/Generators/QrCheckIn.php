<?php

namespace App\Generators;

use App\Interfaces\AsImage;
use App\Models\Delegator;
use App\Traits\Generator;
use Intervention\Image\Image;
use Intervention\Image\Facades\Image as Gambar;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCheckIn implements AsImage
{
    use Generator;

    const WIDTH = 800;
    const HEIGHT = 800;

    public function __construct(
        public Delegator $delegator,
        public string $format = 'jpg'
    )
    {
        
    }

    public function image(): Image
    {
        $image = Gambar::make(resource_path('templates/QRCHECKIN.jpg'))->resize(self::WIDTH, self::HEIGHT);

        $qr = Gambar::make(base64_encode(QrCode::style('round')
                        ->format('png')
                        ->size(800)
                        ->color(51,41,75)
                        ->eyeColor(0, 148, 28, 138, 20, 127, 74)
                        ->eyeColor(1, 148, 28, 138, 20, 127, 74)
                        ->eyeColor(2, 148, 28, 138, 20, 127, 74)
                        ->generate($this->delegator->code->id)))->resize(440, 440);
        $image->insert($qr, 'center', -60, 30);
        unset($qr);

        $image->text(strtoupper($this->delegator->name), 330, 697, function($font){
            $font->file(resource_path('templates/fonts/bold.ttf'));
            $font->size(24);
            $font->color('#33294b');
            $font->align('center');
            $font->valign('middle');
        });

        return $image->encode($this->format, 100);
    }
}