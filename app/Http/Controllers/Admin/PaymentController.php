<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) return datatables()->of(Payment::withCount('participants')->with('delegators')->with('owner'))->addColumn('amount', function($data){
            return $data->participants_count * config('konfer.htm');
        })->make();

        return view('admin.payment');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
            'file' => 'required_if:action,reject|mimetypes:application/pdf,image/*'
        ]);

        DB::transaction(function() use($payment, $request) {

            if($request->get('action') == 'accept'){

                $payment->update([
                    'accepted_at' => Carbon::now()
                ]);

                activity()->performedOn($payment)->log('Pembayaran diterima.');

            }

            else if($request->get('action') == 'reject'){


                activity()->performedOn($payment)->withProperties([
                    'old_file' => $payment->getAttributes()['bukti_transfer'],
                ])->log('Pembayaran diterima dengan perbaikan.');

                $payment->update([
                    'accepted_at' => Carbon::now(),
                    'bukti_transfer' => $request->file('file')->store('', ['disk' => 'bukti_transfer'])
                ]);

            }

            $payment->delegators->each(function(Delegator $data){
                $data->steps()->create([
                    'step' => DelegatorStep::$LUNAS
                ]);
            });

        });

        return response([], 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
