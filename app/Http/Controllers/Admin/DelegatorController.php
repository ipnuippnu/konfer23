<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use App\Models\Participant;
use App\Services\Saedo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DelegatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) return datatables()->of(Delegator::query()->withCount('participants')->with('step')->orderBy('created_at', 'asc'))->make(true);
        return view('admin.delegators');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Delegator $delegator)
    {
        $kecamatan = Saedo::get('konferapi/kecamatan/' . $delegator->address_code)['data'];

        $delegator->load(['participants', 'steps' => function($q) {
            $q->orderBy('created_at', 'desc');
        }]);

        $participants = $delegator->participants->map(function(Participant $participant) {
            if($participant->null) {
                $participant->sertifikat_makesta = null;
            }
            else {
                $participant->sertifikat_makesta = Storage::url($participant->sertifikat_makesta);
            }
            return $participant;
        });
        
        return view('admin.delegators_single', compact('delegator', 'participants', 'kecamatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delegator $delegator)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
            'reason' => 'required_if:action,reject',
            'status_sp' => 'required_if:action,accept|in:1,0'
        ]);

        if($delegator->banom == 'ippnu') {
            $request->validate([
                'participants' => 'required|array|min:1',
                'participants.*.name' => 'required|exists:participants,id',
                'participants.*.value' => 'required|in:1,2,3',
            ]);
        }

        DB::transaction(function() use($request, $delegator) {

            if($request->get('action') == 'accept'){

                if($delegator->banom == 'ippnu') {
                    foreach ($request->get('participants') as $participant) {
                        $participant = Participant::findOrFail($participant['name']);
                        $participant->sertifikat_status = $participant['value'];
                        $participant->save();
                    }
                }

                $delegator->is_valid = !!$request->get('status_sp');
                $delegator->save();

                $delegator->steps()->create([
                    'step' => DelegatorStep::$DITERIMA
                ]);

                activity()->performedOn($delegator)->log('Berkas Diterima');
            }
            else if($request->get('action') == 'reject'){

                if($delegator->attempt == 4)
                    $delegator->steps()->create([
                        'step' => DelegatorStep::$DIBLOKIR,
                        'keterangan' => $request->get('reason')
                    ]);
                else
                    $delegator->steps()->create([
                        'step' => DelegatorStep::$DITOLAK,
                        'keterangan' => $request->get('reason')
                    ]);

                activity()->performedOn($delegator)->log('Berkas Ditolak');
            }

        });

        return response()->json([], 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
