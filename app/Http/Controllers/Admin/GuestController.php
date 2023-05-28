<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\InvitationGeneratorJob;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\Facades\Image;
use TCPDF;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) return datatables()->eloquent(Guest::with('code'))->editColumn('created_at', fn($data) => $data->created_at->format('Y-m-d H:i:s'))->toJson();
        
        return view('admin.guests', [
            'vip' => Guest::whereType('vip')->count(),
            'vvip' => Guest::whereType('vvip')->count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'jabatan' => 'nullable|string',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
            'type' => 'required|in:vip,vvip'
        ]);

        DB::beginTransaction();
        $result = Guest::create([
            'name' => trim($request->get('name')),
            'jabatan' => trim($request->get('jabatan')),
            'address' => trim($request->get('alamat')),
            'type' => trim($request->get('type')),
            'keterangan' => trim($request->get('keterangan')),
        ])->name;
        DB::commit();

        return response()->json([
            'status' => true,
            'message' => "$result berhasil ditambahkan."
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Guest $guest)
    {
        InvitationGeneratorJob::dispatchSync($guest);

        return response()->file(Storage::disk('undangan')->path($guest->invitation));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $guest)
    {
        $guest->delete();

        return response()->json([
            'status' => true
        ]);
    }

    public function download(Request $request)
    {
        $guests = Guest::whereIn('id', $request->get('data'))->get();


        

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
        $guests->each(function(Guest $guest) use(&$now, &$pdf) {


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

        $filename = \Str::random(80) . '.pdf';
        Storage::disk('public')->put($filename, $pdf->Output('', 'S'));

        return response()->json([
            'status' => true,
            'link' => Storage::disk('public')->url($filename)
        ]);

    }
}
