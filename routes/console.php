<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use Carbon\Carbon;
use App\Models\Participant;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Facades\Image;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('anzay', function(){
    $participants = new Collection();

    //  Delegator::with('code', 'payment')->join('delegator_steps as s', 'delegators.id', '=', 's.delegator_id')->select('delegators.id', 'delegators.name', 's.created_at')->where('s.step', DelegatorStep::$LUNAS)->orderBy('s.created_at', 'ASC')->whereBetween('s.created_at', [Carbon::parse('2001-07-08'), Carbon::parse('2023-05-26 15:00:00')])->get()->each(function(Delegator $delegator) use(&$participants) {

    //     $participants = $participants->merge($delegator->participants);
        
    // });

    Delegator::with('code', 'payment')->join('delegator_steps as s', 'delegators.id', '=', 's.delegator_id')->select('delegators.id', 'delegators.name', 's.created_at')->where('s.step', DelegatorStep::$LUNAS)->orderBy('s.created_at', 'ASC')->whereBetween('s.created_at', [Carbon::parse('2023-05-26 15:00:01'), Carbon::now()])->get()->each(function(Delegator $delegator) use(&$participants) {

        $participants = $participants->merge($delegator->participants);
        
    });
    
    Delegator::whereDoesntHave('steps', function($q){
        
        $q->where('step', DelegatorStep::$LUNAS);

    })->get()->each(function(Delegator $delegator) use(&$participants) {

        $participants = $participants->merge($delegator->participants);
        
    });

    // dd($participants->count());

    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    
    $pdf->setPrintFooter(false);
    $pdf->setPrintHeader(false);
    $pdf->setAutoPageBreak(false);

    $belakang = (string) Image::make(resource_path('templates/idcard-invers.jpg'))->rotate(-90)->flip('v')->encode('jpg');

    $tinggi = 57;
    $lebar = 89;

    $participants->each(function(Participant $participant, $k) use(&$pdf, $belakang, $tinggi, $lebar) {

        
        $i = ($k % 5) + 1;
        
        if($i == 1)
        {
            $pdf->AddPage();
        }

        $atasan = 0;
        if($i > 1) $atasan = ($tinggi) * ($i - 1);

        $qr = Storage::path("temp/{$participant->id}.png");

        QrCode::style('round')
            ->format('png')
            ->size(800)
            ->color(51,41,75)
            ->eyeColor(0, 148, 28, 138, 20, 127, 74)
            ->eyeColor(1, 148, 28, 138, 20, 127, 74)
            ->eyeColor(2, 148, 28, 138, 20, 127, 74)
            ->generate($participant->code->id, $qr);


        $depan = Image::make(resource_path('templates/idcard-peserta-front.png'));
        
        $depan->text($participant->limit_name, 300, 389, function($font){
            $font->file(resource_path('templates/fonts/bold.ttf'));
            $font->size(33);
            $font->color('#000');
            $font->align('center');
            $font->valign('top');
        });

        $depan->text($participant->delegator->name, 300, 465, function($font){
            $font->file(resource_path('templates/fonts/bold.ttf'));
            $font->size(27);
            $font->color('#fff');
            $font->align('center');
            $font->valign('top');
        });

        $depan->insert(\Image::make($qr)->resize(300, 300), 'center', 8, 205);

        $depan->text($participant->delegator->address_code . " (STEP_2)", 380, 953, function($font){
            $font->file(resource_path('templates/fonts/bold.ttf'));
            $font->size(25);
            $font->color('#2c1156');
            $font->align('center');
            $font->valign('top');
        });

        $depan = $depan->rotate(90)->flip('v')->encode('jpg');


        $pdf->Image("@" . $belakang, 7, ((2 * $i) + $atasan), $lebar, $tinggi);
        
        $pdf->Image("@" . $depan, (210 - 14 - $lebar), ((2 * $i) + $atasan), $lebar, $tinggi);

    });


    Storage::put('gas.pdf', $pdf->Output('', 'S'));

});