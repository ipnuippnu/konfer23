<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DelegatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) return datatables()->of(Delegator::query()->withCount('participants')->with('step'))->make(true);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delegator $delegator)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
            'reason' => 'required_if:action,reject'
        ]);

        DB::transaction(function() use($request, $delegator) {

            if($request->get('action') == 'accept'){
                $delegator->steps()->create([
                    'step' => DelegatorStep::$DITERIMA
                ]);
                activity()->performedOn($delegator)->log('Berkas Diterima');
            }
            else if($request->get('action') == 'reject'){
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
