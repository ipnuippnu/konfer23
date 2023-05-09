<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DelegatorStep extends Model
{
    public static $DIAJUKAN     = 1;
    public static $DITOLAK      = 2;
    public static $DITERIMA     = 3;
    public static $LUNAS        = 4;
    public static $DIBLOKIR     = 5;
    public static $DIBAYAR      = 6;
    
    use HasFactory;

    protected $guarded = ['id'];
    protected $appends = ['info'];

    public function getInfoAttribute()
    {
        switch ($this->step) {
            case self::$DIAJUKAN:
                return "Diajukan";
                break;

            case self::$DITERIMA:
                return "Menunggu Pembayaran";
                break;

            case self::$LUNAS:
                return "Selesai";
                break;

            case self::$DIBLOKIR:
                return "Diblokir";
                break;

            case self::$DIBAYAR:
                return "Dibayar";
                break;

            case self::$DITOLAK:
                return "Ditolak";
                break;
        }
    }
}
