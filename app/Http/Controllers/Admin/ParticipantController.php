<?php

namespace App\Http\Controllers\Admin;

use App\Generators\IdCardParticipant;
use App\Http\Controllers\Controller;
use App\Models\DelegatorStep;
use App\Models\Participant;
use App\Services\Saedo;
use App\Types\Face;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax())
            return datatables()->of(Participant::with(['delegator' => fn($q) => $q->select('delegators.id', 'delegators.name', 'delegators.address_code')])->select('participants.id', 'participants.name', 'participants.jabatan', 'participants.delegator_id', 'participants.born_date', 'participants.born_place', 'participants.sertifikat_makesta', 'participants.foto_resmi', 'status')->where('gender', 'P')->orderBy('created_at'))->make(true);

        return view('admin.participants');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(Participant $participant, Request $request)
    {
        $request->validate([
            'type' => 'in:pdf,front,back'
        ]);

        $kecamatan = Saedo::get('konferapi/kecamatan/' . $participant->delegator->address_code)['data'];

        $delegator = $participant->delegator;
        $saedo_participants = collect($delegator->saedo_participants);

        if(!$saedo = $saedo_participants->first(fn($p) => strtolower($p['name']) == strtolower($participant->name))) {
            abort(403, 'Participant not found in SAEDO');
        }

        return match($request->get('type')) {
            'pdf' => response(IdCardParticipant::pdf($participant), 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', "$participant->name") . '.pdf"'
                    ]),

            'back' => response(IdCardParticipant::generate($participant, Face::BACK), 200, [
                        'Content-Type' => 'image/jpeg',
                        'Content-Disposition' => 'attachment; filename="' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', "BELAKANG_$participant->name") . '.jpg"'
                    ]),

            'front' => response(IdCardParticipant::generate($participant, Face::FRONT), 200, [
                        'Content-Type' => 'image/jpeg',
                        'Content-Disposition' => 'attachment; filename="' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', "DEPAN_$participant->name") . '.jpg"'
                    ]),

            default => view('admin.participants_single', [
                'participant' => $participant,
                'delegator' => $participant->delegator,
                'kecamatan' => $kecamatan,
                'sertifikat_makesta' => data_get($saedo, 'training.user_certificate') ? data_get($saedo, 'training.user_certificate') : Storage::url($participant->sertifikat_makesta),
                'saedo' => $saedo
            ])
        };
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Participant $participant, Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:accept',
            'foto_resmi' => 'required|image',
            'tempat_makesta' => 'required',
            'tanggal_makesta' => 'required',
            'no_surat' => 'required',
        ]);

        $participant->update([
            'foto_resmi' => $request->file('foto_resmi')->store('participant'),
            'tempat_makesta' => $data['tempat_makesta'],
            'tanggal_makesta' => $data['tanggal_makesta'],
            'no_surat_makesta' => $data['no_surat'],
            'status' => 'OK'
        ]);

        return response()->noContent();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participant $participant, Request $request)
    {
        abort(404);
    }
}
