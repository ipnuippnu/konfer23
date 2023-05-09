<?php

namespace App\Exports;

use App\Models\Delegator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class DelegatorsExport implements FromCollection, WithEvents, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Delegator::select('id', 'name', 'tingkat', 'banom', 'address_code', 'whatsapp', 'attempt', 'created_at')->withCount('participants')->get()->sortBy(function($item){

            //Diurutkan berdasarkan address code
            return str_pad($item->address_code, 13, '.0000', STR_PAD_RIGHT);

        });
    }

    public function headings(): array
    {
        return [
            'ID', 'Nama', 'Tingkat', 'Banom', 'Kode Daerah', 'No. WhatsApp', 'Revisi', 'Terdaftar Pada', 'Jumlah Peserta'
        ];
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
