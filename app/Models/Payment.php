<?php

namespace App\Models;

use App\Traits\HasCode;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, Uuids, SoftDeletes, HasCode;

    protected $guarded = ['id'];

    public function owner()
    {
        return $this->belongsTo(Delegator::class, 'owner_id');
    }

    public function delegators()
    {
        return $this->hasMany(Delegator::class);
    }

    public function participants()
    {
        return $this->hasManyThrough(Participant::class, Delegator::class);
    }

    public function buktiTransfer() : Attribute
    {
        return Attribute::make(
            get: fn($data) => route('file.bukti_pembayaran', [$data])
        );
    }
}
