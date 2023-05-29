<?php

namespace App\Models;

use App\Traits\HasCode;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Guest extends Model
{
    use HasFactory, Uuids, SoftDeletes, HasCode, LogsActivity;

    protected $guarded = ['id'];

    public function type() : Attribute
    {
        return Attribute::make(
            get: fn($val) => strtoupper($val),
            set: fn($val) => strtolower($val)
        );
    }

    public function event() : Attribute
    {
        return Attribute::make(
            get: fn($val) => strtoupper($val),
            set: fn($val) => strtolower($val)
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('account')->logOnlyDirty()->setDescriptionForEvent(function($event){
            if ($event == 'created') {
                return 'Undangan baru dibuat.';
            }
        
            if ($event == 'updated') {
                return 'Update undangan.';
            }
        
            if ($event == 'deleted') {
                return 'Undangan dihapus.';
            }

            return '';
        });
    }
    
}
