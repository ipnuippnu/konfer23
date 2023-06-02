<?php

namespace App\Http\Controllers\Admin;

use App\Generators\RecapEvent;
use App\Http\Controllers\Controller;
use App\Models\Delegator;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Participant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Permissions\AdminPermission as Admin;
use App\Types\EventParams;
use App\Types\EventTargetType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize(Admin::EVENT_READ);

        if($request->ajax()){
            $participant = Participant::count();
            $delegator = Delegator::count();
            $payment = Payment::count();
            $guests = Guest::count();

            return datatables()->eloquent(Event::query())->addColumn('members_count', fn($q) => $q->members?->count() ?? 0)->editColumn('event_start', fn($v) => $v->event_start?->format('Y-m-d H:i'))->editColumn('event_end', function($data){

                if(!$data->event_end) return 'Buyar';

                if($data->event_start?->isSameDay($data->event_end))
                    return $data->event_end->format('H:i');

                return $data->event_end->format('Y-m-d H:i');
                

            })->addColumn('target_count', fn($data) => match($data->target_type){
                EventTargetType::PARTICIPANTS => $participant,
                EventTargetType::PAYMENTS => $payment,
                EventTargetType::DELEGATORS => $delegator,
                EventTargetType::GUESTS => $guests
            })->addIndexColumn()->toJson();
        }

        return view('admin.event', [
            'types' => EventTargetType::cases(),
            'params' => EventParams::cases()
        ]);

        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:events',
            'start' => 'required|date_format:Y-m-d\TH:i',
            'end' => 'nullable|date_format:Y-m-d\TH:i',
            'type' => ['required', new Enum(EventTargetType::class)],
            'params' => 'array',
            'params.*' => [new Enum(EventParams::class)]
        ]);

        DB::beginTransaction();
        Event::create([
            'name' => $request->get('name'),
            'target_type' => $request->get('type'),
            'target_ids' => [],
            'event_start' => $request->get('start'),
            'event_end' => $request->get('end'),
            'keterangan' => $request->get('keterangan'),
            'params' => collect($request->get('params')),
        ]);
        DB::commit();

        return response()->json([
            'status' => true
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return RecapEvent::generate($event)->Output('a.pdf', 'I');
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
    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json([
            'status' => true
        ]);
    }
}
