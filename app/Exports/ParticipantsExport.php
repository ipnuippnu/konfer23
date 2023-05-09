<?php

namespace App\Exports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ParticipantsExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Participant::select('participants.id', 'participants.name', 'delegators.name as delegasi_asal', 'delegators.address_code as delegasi_code', 'born_place', 'born_date', 'participants.created_at')->join('delegators', 'delegators.id', '=', 'participants.delegator_id')->get()->sortBy(function($item){

            //Diurutkan berdasarkan address code
            return str_pad($item->delegasi_code, 13, '.0000', STR_PAD_RIGHT);

        });
    }

    public function headings(): array
    {
        return ['ID', 'Nama Lengkap', 'Asal Delegasi', 'Kode Alamat', 'Tempat Lahir', 'Tanggal Lahir', 'Terdaftar Pada'];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->autoSize();
            }
        ];
    }
}
