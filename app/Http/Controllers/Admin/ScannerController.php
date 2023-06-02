<?php

namespace App\Http\Controllers\Admin;

use App\Events\QrCodeScanned;
use App\Events\QrGuest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\Delegator;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Participant;
use App\Models\Payment;
use App\Types\EventTargetType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view($request->exists('sapa') ? 'qr.layarsapa' : 'qr.scanner', [
            'data' => Event::select('id', 'name')->get()   
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:codes,id',
            'event' => 'required|string|exists:events,id'
        ]);

        $event = Event::find($request->get('event'));
        $code = Code::find($request->get('code'));

        if($duplicate = DB::table('eventables')->where('event_id', $event->id)->where('eventable_id', $code->content->id)->first())
        {
           activity('qr')->on($code->content)->withProperties(['reason' => 'Duplikat'])->log('QR Ditolak');
           return response()->json([
            'status' => false,
            'title' => 'Duplikasi Ditolak!',
            'message' => 'Kode sebelumnya telah discan ' . Carbon::parse($duplicate->created_at)->diffForHumans()
           ]);
        }

        if(
            ($event->target_type === EventTargetType::DELEGATORS && $code->content instanceof Delegator) ||
            ($event->target_type === EventTargetType::PARTICIPANTS && $code->content instanceof Participant) ||
            ($event->target_type === EventTargetType::GUESTS && $code->content instanceof Guest) ||
            ($event->target_type === EventTargetType::PAYMENTS && $code->content instanceof Payment)
        )
        {
            // if($code->content instanceof Guest)
            // {
            //     broadcast(new QrGuest($code->content));
            // }

            DB::beginTransaction();
            $event->members()->syncWithoutDetaching($code->content);
            DB::commit();

            // broadcast(new QrCodeScanned($code));
            activity('qr')->on($code->content)->log('QR Dipindai');
            return response([
                'status' => true,
                'message' => ($code->content->name ?? $code->content->id) . " berhasil dicatat"
            ], 200);
        }

        activity('qr')->on($code->content)->withProperties(['reason' => 'Event dan kode tidak match'])->log('QR Ditolak');
        return response()->json([
            'status' => false,
            'title' => 'Event tidak ada!',
            'message' => 'Kode tidak tersedia pada event ini'
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if($event = Event::find($id))
        {
            return response()->json([
                'type' => $event->target_type->value,
                'data' => match($event->target_type){
                    EventTargetType::DELEGATORS => Delegator::join('codes', function($join){
                        $join->on('delegators.id', '=', 'codes.content_id')->where('content_type', Delegator::class);
                    })->get(['delegators.*', 'codes.id as code']),
    
                    EventTargetType::PARTICIPANTS => Participant::join('codes', function($join){
                        $join->on('participants.id', '=', 'codes.content_id')->where('content_type', Participant::class);
                    })->get(['participants.*', 'codes.id as code']),
    
                    EventTargetType::PAYMENTS => Payment::join('codes', function($join){
                        $join->on('payments.id', '=', 'codes.content_id')->where('content_type', Payment::class);
                    })->get(['payments.*', 'codes.id as code']),
    
                    EventTargetType::GUESTS => Guest::join('codes', function($join){
                        $join->on('guests.id', '=', 'codes.content_id')->where('content_type', Guest::class);
                    })->get(['guests.name', 'guests.type', 'guests.event', 'codes.id as code'])
                }
            ]);
        }

        abort(404);
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
    public function destroy(string $id)
    {
        //
    }
}
