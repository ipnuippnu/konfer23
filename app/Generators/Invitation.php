<?php

namespace App\Generators;

use App\Interfaces\AsImage;
use App\Models\Guest;
use App\Traits\Generator;
use App\Types\Event;
use App\Types\Face;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Image;
use Intervention\Image\Facades\Image as Gambar;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use TCPDF;

class Invitation implements AsImage
{
    use Generator;

    public static $WIDTH = 1674;
    public static $HEIGHT = 1180;

    private $cache_name;

    public function __construct(
        public Guest $guest,
        private Face $face = Face::FRONT,
        private string $format = 'jpg'
    ){
        $this->cache_name = sha1("invitation-{$this->guest->id}-{$this->guest->updated_at}-$this->format-{$this->face->name}");
    }

    public function image(): Image
    {
        return Cache::remember($this->cache_name, (3600 * 24), function(){
            $img = Gambar::make(resource_path("templates/undangan/{$this->guest->event}/{$this->face->name}.jpg"))->resize(self::$WIDTH, self::$HEIGHT);

            if($this->face === Face::FRONT)
            {
                //Generate QRCode
                $qr = Gambar::make(base64_encode(QrCode::style('round')
                        ->format('png')
                        ->size(800)
                        ->color(51,41,75)
                        ->eyeColor(0, 148, 28, 138, 20, 127, 74)
                        ->eyeColor(1, 148, 28, 138, 20, 127, 74)
                        ->eyeColor(2, 148, 28, 138, 20, 127, 74)
                        ->generate($this->guest->code->id)))->resize(275, 275);
                $img->insert($qr, 'center', 500, -52);
                unset($qr);

                $this->addTextBox($img, $this->guest->name);

            }

            return $img->encode($this->format);
        });
    }
    
    /**
     * addTextBox
     *
     * @param  Image $image
     * @param  string $text
     * @param  int $maxCharPerLine
     * @return void
     */
    private function addTextBox(Image &$image, string $text, int $maxCharPerLine = 25)
    {
        $tulisan = Gambar::canvas(1200, 1200, 0);
        $splits = explode(' ', $text);
        $currentLine = '';
        $lines = 0;

        $batasPerLine = 85;

        $text_jadi = "Yth.\n";

        foreach($splits as $word)
        {
            $currentLine .= " $word";
            if(strlen(trim($currentLine)) > $maxCharPerLine)
            {
                $text_jadi .= substr(trim($currentLine), 0, (-1 + strlen($word) * -1)) . "\n";
                $lines++;
                $currentLine = $word;
            }
        }

        $text_jadi .= $currentLine;
        $atas = -$batasPerLine -($batasPerLine * $lines) ;

        $tulisan->text($text_jadi, 600, (600 + $atas), function($font){
            $font->file(resource_path('templates/fonts/bold.ttf'));
            $font->size(80);
            $font->color('#d7b033');
            $font->align('center');
            $font->valign('middle');
        });

        $tulisan->resize(480, 480);
        $image->insert($tulisan, 'center', 415, 310);
        $tulisan->destroy();

    }

    public static function pdf(Guest $guest)
    {
        return Cache::remember("invitation-{$guest->id}-{$guest->updated_at}", (3600 * 24), function() use($guest) {
            $front = self::generate($guest, Face::FRONT, 'jpg');
            $back = self::generate($guest, Face::BACK, 'jpg');

            $pdf = new TCPDF('L', 'mm', [self::$WIDTH, self::$HEIGHT]);

            $pdf->setCreator('PC IPNU-IPPNU Trenggalek');
            $pdf->setAuthor('Panitia Konferensi');

            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(false);
            $pdf->setMargins(0, 0, 0, true);
            $pdf->SetAutoPageBreak(false, 0);

            $pdf->AddPage();
            $pdf->Image('@' . $front, 0, 0, $pdf->getPageWidth() + 1, $pdf->getPageHeight());

            $pdf->AddPage();
            $pdf->Image('@' . $back, 0, 0, $pdf->getPageWidth() + 1, $pdf->getPageHeight());

            return $pdf->Output($guest->name, 'S');
        });
    }
}