<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $totalPeserta = Participant::whereHas('delegator', fn($q1) => $q1->whereHas('step', fn($q2) => $q2->where('step', DelegatorStep::$LUNAS)))->count();

        $perKecamatan = Cache::remember('perKecamatan', 60, function() use($totalPeserta) {

            $data = collect(config('konfer.kecamatan'))->map(function($name, $code) use($totalPeserta) {

                $warna = sprintf("#%02x%02x%02x", rand(0, 255), rand(0, 255), rand(0, 255));
                return [
                    'total' => $total = Delegator::where('address_code', 'LIKE', "{$code}%")->whereHas('step', fn($q) => $q->where('step', DelegatorStep::$LUNAS))->withCount('participants')->get()->sum('participants_count'),
                    'persentase' => $total == 0 ? 0 : $total / $totalPeserta * 100,
                    'warna' => $warna,
                    'name' => "Kecamatan {$name}"
                ];

            })->sortByDesc('total')->values();

            // $data = Delegator::select('id', 'name', 'address_code')->withCount('participants')->orderByRaw('CHAR_LENGTH(address_code)')->get()->groupBy(function($item){
            //     return substr($item['address_code'],0,8);
            // })->map(function(Collection $item) use($totalPeserta) {
            //     return $item->reduce(function($carry, $item) use($totalPeserta)
            //     {
            //         if($carry == null){
            //             $warna = sprintf("#%02x%02x%02x", rand(0, 255), rand(0, 255), rand(0, 255));
            //             $carry = [
            //                 'address_code' => $item['address_code'],
            //                 'name' => preg_replace("/^(\w*)\s(\w*)\s(.*)/", "Kecamatan $3", $item['name']),
            //                 'total' => 0,
            //                 'warna' => $warna
            //             ];
            //         }
    
                    
            //         $carry['total'] += $item['participants_count'];
            //         $carry['persentase'] = round($carry['total'] / $totalPeserta * 100, 1);
    
            //         return $carry;
            //     });
            // })->sortByDesc('total')->values();

            return [
                'data' => $data,
                'updated_at' => Carbon::now()
            ];
        }); 

        return view('admin.dashboard', [
            'jumlah' => [
                'pimpinan' => Delegator::count(),
                'peserta' => $totalPeserta,
                'verified' => Delegator::whereHas('steps', function($q){
                    $q->where('step', DelegatorStep::$DITERIMA);
                })->count()
            ],  
            'bayar' => [
                'sudah' => $sudah = Delegator::whereHas('payment', function($q){ $q->whereNotNull('accepted_at'); })->withCount('participants')->get()->sum('participants_count') * config('konfer.htm'),

                'belum' => $belum = Delegator::whereDoesntHave('payment', function($q){ $q->whereNotNull('accepted_at'); })->withCount('participants')->get()->sum('participants_count') * config('konfer.htm'),

                'data' => [$sudah, $belum]
            ],
            'perkecamatan' => $perKecamatan,
            'activities' => Activity::latest()->limit(5)->get()
        ]);
    }
}
