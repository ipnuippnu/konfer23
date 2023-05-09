<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Uuids, CausesActivity, LogsActivity, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'permission' => 'json'
    ];

    public function avatar() : Attribute
    {
        return Attribute::make(get: function($val){
            if(Storage::disk('public')->exists($val)) return Storage::disk('public')->url($val);

            return asset('img/admin-default.png');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll()->useLogName('account')->logOnlyDirty()->setDescriptionForEvent(function($event){
            if ($event == 'created') {
                return 'Admin baru ditambahkan';
            }
        
            if ($event == 'updated') {
                return 'Update profil admin';
            }
        
            if ($event == 'deleted') {
                return 'Admin dihapus';
            }

            return '';
        });
    }

    public function getLogsAttribute()
    {
        return Activity::where(function($query){
            $query->where('causer_type', get_class())->where('causer_id', $this->id);
        })->orWhere(function($query){
            $query->where('subject_type', get_class())->where('subject_id', $this->id);
        })->get();
    }
}
