<?php

namespace App\Http\Controllers;

use App\Http\Requests\PembayaranRequest;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use App\Models\Participant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{

    public function index(Request $request)
    {
        $data = [
            'delegator' => $me = Delegator::find(\Sso::credential()->id),
            'partners' => Delegator::where('address_code', $me->address_code)->orWhere('address_code', 'LIKE', $me->address_code . '%')->get()->map(function(Delegator $delegator) use($me) {
                return [
                    "id" => $delegator->id, "name" => $delegator->name, "members" => $jumlah = $delegator->participants()->count(), "price" => (60000 * $jumlah), "is_me" => $delegator->id == $me->id
                ];
            }),
            'step' => $step = $me->step->step,
            'editable' => $step == DelegatorStep::$DITERIMA,
            'payment' => $me->payment,
            'current' => 0
        ];

        return view('payment', $data);
    }

    public function create(PembayaranRequest $request)
    {
        $delegator = Delegator::find(\Sso::credential()->id);
        if($delegator->step->step != DelegatorStep::$DITERIMA)
        {
            abort(403, "Permintaan ditolak karena kondisi sedang tidak diizinkan melakukan perubahan data.");
        }

        DB::beginTransaction();

        $current = 0;

        $payment = Payment::create([
            'owner_id' => $delegator->id,
            'bukti_transfer' => $request->file('bukti_pembayaran')->store('', ['disk' => 'bukti_transfer']),
            'total' => 0
        ]);

        $current += $delegator->participants()->count() * 60000;

        if(is_array($request->get('ids'))) foreach($request->get('ids') as $id)
        {
            $member = Delegator::find($id);
            $current += $member->participants()->count() * 60000;
            $member->update([
                'payment_id' => $payment->id
            ]);
        }

        $payment->total = $current;
        $payment->save();

        $delegator->update([
            'payment_id' => $payment->id
        ]);
        $delegator->steps()->create([
            'step' => DelegatorStep::$DIBAYAR
        ]);

        DB::commit();

        return response()->json([
            'status' => true
        ]);
    }

    public function buktiTransfer()
    {
        $delegator = Delegator::find(\Sso::credential()->id);
        if($delegator->payment)
            return Storage::disk('bukti_transfer')->response($delegator->payment->bukti_transfer);

        abort(404);
    }
}
