<?php

namespace App\Exports;

use App\Models\Delegator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class PaymentsExport implements FromCollection, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Delegator::all()->sortBy(function($delegator){

            //Diurutkan berdasarkan address code
            return str_pad($delegator->address_code, 13, '.0000', STR_PAD_RIGHT);

        })->map(function(Delegator $delegator){

            return [
                $delegator->name,
                $delegator->address_code,
                $delegator?->payment ? 'SUDAH' : 'BELUM',
                $delegator?->payment->owner->name ?? '-',
                $count = $delegator->participants->count(),
                ($count * config('konfer.htm'))
            ];

        });
    }

    public function headings(): array
    {
        return [
            'Nama Pimpinan',
            'Kode Wilayah',
            'Status Pembayaran',
            'Dibayarkan Oleh',
            'Jumlah Peserta',
            'Total'
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
