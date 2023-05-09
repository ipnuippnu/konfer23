<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Delegator extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    protected $guarded = [];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function steps()
    {
        return $this->hasMany(DelegatorStep::class);
    }

    public function step()
    {
        return $this->hasOne(DelegatorStep::class)->latest(); 
    }

    public function suratPengesahan() : Attribute
    {
        return Attribute::make(
            get: fn($val) => route('file.sp', [$val])
        );
    }

    public function suratTugas() : Attribute
    {
        return Attribute::make(
            get: fn($val) => route('file.st', [$val])
        );
    }
}
