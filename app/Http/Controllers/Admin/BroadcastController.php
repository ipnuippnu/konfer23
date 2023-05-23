<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappJob;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;

class BroadcastController extends Controller
{
    private const PESAN_BAYAR = <<<EOL
    📣   📣   📣
    *Assalamu'alaikum Wr. Wb.*

    Hai gimana kabarnya rekan/ita? Mimin mau ngasih tau nih kalau delegasi dari :

    {delegators}

    Sampai saat ini *belum melakukan pembayaran* .😥
    Yuk segera melunasi tanggungan ya supaya delegasi dari pimpinan diatas bisa mengikuti Konferensi Cabang nanti.🤩
    
    Makasih ya, salam hangat dari mimin.
    🥰🥰🥰

    🔗 edo.pelajartrenggalek.or.id
    EOL;

    private const PESAN_REVISI = <<<EOL
    📣   📣   📣
    *Assalamu'alaikum Wr. Wb.*

    Hai gimana kabarnya rekan/ita? Mimin mau ngasih tau nih kalau delegasi dari :

    {delegators}

    Perlu melakukan *revisi berkas* . 😥
    Yuk segera perbaiki berkas yang salah untuk melanjutkan ketahap selanjutnya.🤩
    
    Makasih ya, salam hangat dari mimin.
    🥰🥰🥰

    🔗 edo.pelajartrenggalek.or.id
    EOL;

    public function __invoke()
    {
        return view('admin.broadcast', [
            'logs' => Activity::where('log_name', 'broadcast')->latest()->get(),
            'can_send_revisions' => Delegator::whereDoesntHave('step', fn($q) => $q->where('step', DelegatorStep::$DITERIMA))->count() > 0,
            'can_send_unpaids' => Delegator::whereNull('payment_id')->count() > 0
        ]);
    }

    public function sendUnpaids()
    {
        /** @var Collection $unpaided */
        $unpaided = Delegator::whereNull('payment_id')->get();

        $unpaided->each( function(Collection $delegators, $phone) {

            $pesan = self::PESAN_BAYAR;
            $pesan = str_replace("{delegators}", $delegators->reduce(function($carry, Delegator $delegator){
                return $carry . "*- $delegator->name*\n";
            }, ""), $pesan);

            dispatch(new SendWhatsappJob($phone, $pesan));

        });


        activity('broadcast')
            ->withProperties($unpaided->map(fn(Collection $data) => $data->pluck('name'))->toArray())
            ->log("Kirim broadcast pembayaran");

        return response()->json([], 204);
    }

    public function sendRevisions()
    {
        /** @var Collection $unpaided */
        $unpaided = Delegator::whereDoesntHave('step', fn($q) => $q->where('step', DelegatorStep::$DITERIMA))->get();

        dd($unpaided->map(fn($a) => $a->name));

        $unpaided->each( function(Collection $delegators, $phone) {

            $pesan = self::PESAN_REVISI;
            $pesan = str_replace("{delegators}", $delegators->reduce(function($carry, Delegator $delegator){
                return $carry . "*- $delegator->name*\n";
            }, ""), $pesan);

            dispatch(new SendWhatsappJob($phone, $pesan));

        });


        activity('broadcast')
            ->withProperties($unpaided->map(fn(Collection $data) => $data->pluck('name'))->toArray())
            ->log("Kirim broadcast revisi");
        return response()->json([], 204);
    }
}
