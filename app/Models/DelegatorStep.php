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
}
