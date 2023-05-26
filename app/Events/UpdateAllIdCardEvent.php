<?php

namespace App\Events;

use Carbon\Carbon;

class UpdateAllIdCardEvent
{
    /**
     * !! HANYA CETAK YANG SUDAH LUNAS
     * NOTE: Diurutkan dari PIMPINAN tanggal yang paling lunas verifikasi
     * 
     * Jika semua dikosongkan maka: $from kosong dan $to sekarang
     *
     * @param  Carbon|null $from
     * @param  Carbon|null $to
     * @return void
     */
    public function __construct(
        public Carbon|null $from = null,
        public Carbon|null $to = null
    )
    {

    }

}
