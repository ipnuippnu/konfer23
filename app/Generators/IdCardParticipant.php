<?php

namespace App\Generators;

use App\Interfaces\AsImage;
use App\Models\Participant;
use App\Traits\Generator;
use App\Types\Face;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Image;
use Intervention\Image\Facades\Image as Gambar;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use TCPDF;

class IdCardParticipant implements AsImage
{
    use Generator;

    public static $WIDTH = 500;
    public static $HEIGHT = 803;

    private $cache_name;

    public function __construct(
        public Participant $participant,
        private Face $face = Face::FRONT,
        private string $format = 'jpg'
    ){
        $this->cache_name = sha1("idcard-{$this->participant->id}-{$this->participant->updated_at}-$this->format-{$this->face->name}");
    }

    public function image(): Image
    {
        return Cache::remember("$this->cache_name", (3600 * 24), function(){

            $img = Gambar::make(resource_path("templates/idcard/participants/{$this->face->name}.jpg"))->resize(self::$WIDTH, self::$HEIGHT);

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
                        ->generate($this->participant->code->id)))->resize(260, 260);
                $img->insert($qr, 'center', 8, 165);
                unset($qr);
                
                //Nama Peserta
                $img->text($this->participant->limit_name, 250, 301, function($font){
                    $font->file(resource_path('templates/fonts/bold.ttf'));
                    $font->size(32);
                    $font->color('#000');
                    $font->align('center');
                    $font->valign('top');
                });
        
                //Delegasi Asal
                $img->text($this->participant->delegator->name, 250, 367, function($font){
                    $font->file(resource_path('templates/fonts/bold.ttf'));
                    $font->size(20);
                    $font->color('#fff');
                    $font->align('center');
                    $font->valign('top');
                });
        
                //Kode Admin
                $img->text($this->participant->delegator->address_code . " (" . ($this->participant?->delegator?->payment->code->id ?? 'STEP_2') . ") ", 308, 757, function($font){
                    $font->file(resource_path('templates/fonts/bold.ttf'));
                    $font->size(20);
                    $font->color('#000');
                    $font->align('center');
                    $font->valign('top');
                });

            }

            return $img->encode($this->format);

        });

    }

    public static function pdf(Participant $participant)
    {
        return Cache::remember("invitation-{$participant->id}-{$participant->updated_at}", (3600 * 24), function() use($participant) {

            $front = self::generate($participant, Face::FRONT, 'jpg');
            $back = self::generate($participant, Face::BACK, 'jpg');

            $pdf = new TCPDF('P', 'mm', [self::$WIDTH, self::$HEIGHT]);

            $pdf->setCreator('PC IPNU-IPPNU Trenggalek');
            $pdf->setAuthor('Isnu Nasrudin');

            $pdf->setPrintFooter(false);
            $pdf->setPrintHeader(false);
            $pdf->setMargins(0, 0, 0, true);
            $pdf->SetAutoPageBreak(false, 0);

            $pdf->AddPage();
            $pdf->Image('@' . $front, 0, 0, $pdf->getPageWidth() + 1, $pdf->getPageHeight());

            $pdf->AddPage();
            $pdf->Image('@' . $back, 0, 0, $pdf->getPageWidth() + 1, $pdf->getPageHeight());

            return $pdf->Output($participant->name, 'S');

        });
    }
}