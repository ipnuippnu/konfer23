<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delegator;
use App\Models\DelegatorStep;
use App\Models\Participant;
use App\Permissions\AdminPermission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        if(count(auth()->user()->permission) === 1 && auth()->user()->permission[0] == AdminPermission::SCANNER)
        {
            return redirect()->route('admin.qr.index');
        }

        $perKecamatan = Cache::remember('perKecamatan', 60, function() {

            $data = collect(config('konfer.kecamatan'))->map(function($name, $code) {

                $warna = sprintf("#%02x%02x%02x", rand(0, 255), rand(0, 255), rand(0, 255));
                $total = Delegator::where('address_code', 'LIKE', "{$code}%")->withCount('participants')->get()->sum('participants_count');
                return [
                    'total' => $total,
                    'persentase' => $total == 0 ? 0 : round($total / Participant::count() * 100, 1),
                    'warna' => $warna,
                    'name' => "Kecamatan {$name}",
                    'peserta' => [
                        'ipnu' => Delegator::where('address_code', 'LIKE', "{$code}%")->withCount(['participants' => fn($q) => $q->whereGender('L')])->get()->sum('participants_count'),
                        'ippnu' => Delegator::where('address_code', 'LIKE', "{$code}%")->withCount(['participants' => fn($q) => $q->whereGender('P')])->get()->sum('participants_count'),
                        'total' => $total
                    ],
                    'delegators' => [
                        'ipnu' => Delegator::where('address_code', 'LIKE', "{$code}%")->whereBanom('ipnu')->count(),
                        'ippnu' => Delegator::where('address_code', 'LIKE', "{$code}%")->whereBanom('ippnu')->count(),
                        'total' => Delegator::where('address_code', 'LIKE', "{$code}%")->count()
                    ]
                ];

            })->sortByDesc('total')->values();

            return [
                'data' => $data,
                'updated_at' => Carbon::now()
            ];
        });

        return view('admin.dashboard', [
            'jumlah' => [
                'pimpinan' => Delegator::count(),
                'peserta' => [
                    'ipnu' => Participant::whereGender('L')->count(),
                    'ippnu' => Participant::whereGender('P')->count(),
                    'total' => Participant::count()
                ],
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
            'activities' => Activity::with('causer')->latest()->limit(5)->get()
        ]);
    }
}
