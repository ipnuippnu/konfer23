<?php

namespace App\Models;

use App\Traits\HasCode;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Guest extends Model
{
    use HasFactory, Uuids, SoftDeletes, HasCode;

    protected $guarded = ['id'];
    
}
