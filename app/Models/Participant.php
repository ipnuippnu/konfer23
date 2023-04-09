<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    protected $guarded = ['id'];

    public function delegator()
    {
        return $this->belongsTo(Delegator::class);
    }

    public function name() : Attribute
    {
        return Attribute::make(
            set: fn(string $value) => ucwords(strtolower($value))
        );
    }

    public function bornPlace() : Attribute
    {
        return Attribute::make(
            set: fn(string $value) => ucwords(strtolower($value))
        );
    }

    public function jabatan() : Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucwords($value)
        );
    }
}
