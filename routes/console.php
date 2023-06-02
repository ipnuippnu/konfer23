<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Guest;
use App\Models\Participant;
use App\Models\User;
use App\Permissions\AdminPermission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Facades\Image;

use App\Generators\QrCheckIn;
use App\Jobs\SendWhatsappJob;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Illuminate\Database\Eloquent\Collection;

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

Artisan::command('toganjelto', function(){

    

    $pdf = new TCPDF('P', 'mm', 'F4', true, 'UTF-8', false);
    
    $pdf->setPrintFooter(false);
    $pdf->setPrintHeader(false);
    $pdf->setAutoPageBreak(false);
    $pdf->setMargins(2, 2, 2);
    
    function addTextBox(&$image, $text, $positionx, $positiony, $maxCharPerLine)
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

    $now = 1;
    Guest::all()->each(function(Guest $guest) use(&$now, &$pdf) {


        $luar = Image::make(resource_path('templates/undangan-depan.png'));



        $luar->resize(4243, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $qr = Image::make(base64_encode(QrCode::style('round')
            ->format('png')
            ->size(800)
            ->color(51,41,75)
            ->eyeColor(0, 148, 28, 138, 20, 127, 74)
            ->eyeColor(1, 148, 28, 138, 20, 127, 74)
            ->eyeColor(2, 148, 28, 138, 20, 127, 74)
            ->generate($guest->code->id)))->resize(700, 700);
    
        $luar->insert($qr, 'center', 1310, -220);
    
        addTextBox($luar, $guest->name, 3190, 2300, 25);


        if($now === 2)
        {
            $now = 1;
            $pdf->Image('@' . $luar->encode('jpg'), $pdf->GetX(), 147, $pdf->getPageWidth() - 4);
        }

        else

        {
            $pdf->AddPage();
            $now = 2;
            $pdf->Image('@' . $luar->encode('jpg'), $pdf->GetX(), $pdf->GetY(), $pdf->getPageWidth() - 4);
        }
        
    });

    

    Storage::put('toganjelto.pdf', $pdf->Output('', 'S'));

});

Artisan::command('akun:scanner', function(){

    function generateUsername() {
        $username = "Sekretariat";
        $number = 1;
      
        // Mencari nomor urut yang belum digunakan
        while (User::where('name', $username . " " . str_pad($number, 2, '0', STR_PAD_LEFT))->exists()) {
          $number++;
        }
      
        // Menghasilkan nama pengguna dengan nomor urut yang tepat
        return str_pad($number, 2, '0', STR_PAD_LEFT);
      }

    $no_urut = generateUsername();
    $sandi = 'lalisandine';

    $user = User::create([
        'gender' => 'L',
        'permission' => [ AdminPermission::SCANNER ],
        'name' => "Sekretariat $no_urut",
        'jabatan' => 'Hanya Pemindai',
        'password' => bcrypt($sandi),
        'email' => $email = strtolower($no_urut."@qr.com"),
        'phone' => '0000000000' . $no_urut,
    ]);

    dump([
        'nama' => "Sekretariat $no_urut",
        'email' => $email,
        'sandi' => $sandi
    ], Hash::check($sandi, $user->password));

});

Artisan::command('kirimqr', function(){

        $pesan = <<<EOL
        💚  💚  💚
        Assalamu'alaikum rekan/ita

        Berikut kami kirimkan Code QR untuk pengambilan fasilitas KONFERCAB di tempat Check-In.

        Jangan lupa untuk hadir tepat waktu ya, See you on 2 Juni 2023. 👋👋

        ⚠️ _Simpan QR Code ini sebaik mungkin, jangan sampai disalahgunakan oleh orang lain 😨
        EOL;

        Delegator::whereHas('steps', fn($q) => $q->where('step', DelegatorStep::$LUNAS)->whereDate('created_at', [Carbon::parse('11:15'), Carbon::now()]))->get()->groupBy('whatsapp')->each(function(Collection $data, $phone) use($pesan) {
            if($data->count() > 1)
            {
                $jobs = $data->map(function(Delegator $delegator) use($phone) {
                    $img = base64_encode(QrCheckIn::generate($delegator, 'jpg')->getEncoded());
                    return new SendWhatsappJob($phone, "", $img);

                })->prepend(new SendWhatsappJob($phone, $pesan))->toArray();

                Bus::batch($jobs)->onQueue('send_wa')->dispatch();
            }
            else
            {
                $img = base64_encode(QrCheckIn::generate($data[0], 'jpg')->getEncoded());
                SendWhatsappJob::dispatch($phone, $pesan, $img);
            }
        });
});