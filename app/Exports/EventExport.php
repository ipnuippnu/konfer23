<?php

namespace App\Exports;

use App\Models\Delegator;
use App\Models\Event;
use App\Models\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Database\Eloquent\Collection;

class EventExport implements FromCollection
{

    const KOMISI_1 = "ORGANISASI";
    const KOMISI_2 = "PROGRAM";
    const KOMISI_3 = "REKOMENDASI";
    public function __construct(private Event $event )
    {
        
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        /**
         * @var Collection $data
         */
        $data = Participant::all();

        $jumlah_per_ruang = (int) $data->count() / 3;

        $ruang_1 = collect();
        $ruang_2 = collect();
        $ruang_3 = collect();

        $ketua = $data->filter(function(Participant $e){
            return $e->jabatan == 'Ketua';
        });

        $selain_ketua = $data->filter(function(Participant $e){
            return $e->jabatan != 'Ketua';
        })->groupBy('delegator_id');

        $ruang_1 = $ruang_1->merge($ketua);
        
        $selain_ketua_jadi = collect();

        $selain_ketua->each(function(Collection $collection, $delegator_id) use(&$ruang_1, &$ruang_2, &$ruang_3, &$selain_ketua_jadi) {

            if(isset($collection[0]))
            {
                $ruang_2 = $ruang_2->push($collection[0]);
            }


            if(isset($collection[1]))
            {
                $ruang_3 = $ruang_3->push($collection[1]);
            }


            if(isset($collection[2]))
            {
                $ruang_1 = $ruang_1->push($collection[2]);
            }

        });
        
        
        return collect()->merge($ruang_1)->merge($ruang_2)->merge($ruang_3)->map(function(Participant $partisipan){
            return [
                $partisipan->name,
                $partisipan->delegator->name,
                $partisipan->jabatan,
                config('konfer.kecamatan')[substr($partisipan->delegator->address_code, 0, 8)],
                $partisipan->delegator->banom
            ];
        });
        
    }
}
