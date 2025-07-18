<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParticipantRequest;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use App\Services\Saedo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PendaftaranController extends Controller
{
    public function getAvailableParticipants()
    {
        $address = value(function(){
            $credential = \Sso::credential();

            if($credential->type == 'pac') {
                return $credential->address->kecamatan->code;
            }

            if($credential->type == 'pk') {
                return $credential->address->kecamatan->code;
            }

            return $credential->address->code;
        });

        $result = (Saedo::get("konferapi/participants", [
            'gender' => \Sso::credential()->ipnu_ippnu == 'ipnu' ? 'L' : 'P',
            'kecamatan' => $address
        ]));

        return $result;
    }

    public function index()
    {
        $result = $this->getAvailableParticipants();

        $data = [
            'users' => collect($result['data'])->keyBy('ulid'),
            'delegator' => $delegator = Delegator::find(\Sso::credential()->id),
            'step' => $step = $delegator?->step->step,
            'allow_edit' =>  $step === null || ($step == DelegatorStep::$DITOLAK && $delegator->attempt <= 3)
        ];


        return view('daftar', $data);
    }

    public function berkas(Request $request, string $type)
    {
        if($delegator = Delegator::find(\Sso::credential()->id))
        {
            if($type === 'st' && Storage::disk('surat_tugas')->exists($delegator->surat_tugas))
            {
                return Storage::disk('surat_tugas')->response($delegator->surat_tugas);
            }

            if($type === 'sp' && Storage::disk('surat_pengesahan')->exists($delegator->surat_pengesahan))
            {
                return Storage::disk('surat_pengesahan')->response($delegator->surat_pengesahan);
            }
        }
        
        abort(404);
    }

    public function create(ParticipantRequest $request)
    {
        $availableUsers = collect($this->getAvailableParticipants()['data']);

        $participants = collect($request->validated('data'))->filter(function($val){
            return isset($val['user']) && isset($val['foto_resmi']) && isset($val['jabatan']);
        })->filter(function($val) use($availableUsers) {
            return $availableUsers->firstWhere('ulid', $val['user']);
        })->filter(function($val) use($availableUsers) {
            $datav2 = $availableUsers->firstWhere('ulid', $val['user']);
            
            if($datav2['gender'] == 'P') {
                if(!data_get($val, 'sertifikat_makesta') && !data_get($datav2, 'training.user_certificate')) {
                    return false;
                }
            }

            return true;
        })
        ->map(function($val) use($availableUsers) {
            $datav2 = $availableUsers->firstWhere('ulid', $val['user']);
            return [
                'name' => $datav2['name'],
                'gender' => $datav2['gender'],
                'born_place' => $datav2['born_place'],
                'born_date' => $datav2['born_date'],
                'jabatan' => $val['jabatan'],
                'foto_resmi' => $val['foto_resmi']->store(),
                'sertifikat_makesta' => isset($val['sertifikat_makesta']) ? $val['sertifikat_makesta']->store() : null,
            ];
        });

        if($participants->count() < 1)
        {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada satupun peserta yang valid. Silahkan cek kembali kelengkapan peserta yang anda daftarkan.'
            ]);
        }


        DB::beginTransaction();

        $delegator = Delegator::updateOrCreate([
            'id' => \Sso::credential()->id
        ], [
            'name' => \Sso::credential()->name,
            'tingkat' => \Sso::credential()->type,
            'banom' => \Sso::credential()->ipnu_ippnu,
            'surat_tugas' => $request->file('surat_tugas')->store('', ['disk' => 'surat_tugas']),
            'surat_pengesahan' => $request->file('surat_pengesahan')->store('', ['disk' => 'surat_pengesahan']),
            'whatsapp' => preg_replace("/^\+?(0|62)?8/", "628", $request->get('phone')),
            'address_code' => \Sso::credential()->address->code ??\Sso::credential()->address->kecamatan->code,
        ]);

        //Batasi Perubahan Maksimal 3x
        if($delegator->attempt > 3)
        {
            abort(403, 'Melampaui batas perbaikan data. Silahkan menghubungi admin.');
        }

        //Batalkan jika state tidak diizinkan
        if($delegator->step !== null && !($delegator->step->step == DelegatorStep::$DITOLAK && $delegator->attempt <= 3))
        {
            abort(403, 'Permintaan ditolak karena kondisi sedang tidak diizinkan melakukan perubahan data.');
        }

        // dd($delegator->participants);

        //SoftDelete Partisipan

        /**
         * ! MATIKAN PESERTA
         */
        
        if($delegator->participants()->exists())
            $delegator->participants()->delete();

        foreach($participants as $data) 
            $delegator->participants()->create($data);
        

        $delegator->steps()->create([
            'step' => DelegatorStep::$DIAJUKAN
        ]);

        $delegator->increment('attempt');
        DB::commit();

        return response()->json([
            'status' => true
        ]);
    }
}
