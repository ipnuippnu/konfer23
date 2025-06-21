<?php

namespace App\Http\Controllers\Admin;

use App\Generators\IdCardParticipant;
use App\Http\Controllers\Controller;
use App\Models\DelegatorStep;
use App\Models\Participant;
use App\Types\Face;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax())
            return datatables()->of(Participant::with(['delegator' => fn($q) => $q->select('delegators.id', 'delegators.name', 'delegators.address_code')])->select('participants.id', 'participants.name', 'participants.jabatan', 'participants.delegator_id', 'participants.born_date', 'participants.born_place', 'participants.sertifikat_makesta', 'participants.foto_resmi'))->make(true);

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

            default => 'a'
        };
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort(404);
    }
}
