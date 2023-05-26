<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasCode;

class Participant extends Model
{
    use HasFactory, SoftDeletes, Uuids, HasCode;

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

    public function getLimitNameAttribute() {
        $maxLength = 25;

        $tampil = "";
        foreach(explode(' ', $this->name) as $name)
        {
            if(strlen("$tampil $name") > $maxLength)
            {
                $tampil .= " " . substr($name, 0, 1) . ".";
            }
            else
            {
                $tampil .= " $name";
            }
            
        }

        return $tampil;
    }
    
}
