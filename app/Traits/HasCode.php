<?php

namespace App\Traits;

use App\Models\Code;

trait HasCode
{
    public function code()
    {
        return $this->morphOne(Code::class, 'content');
    }

    protected static function bootHasCode()
    {
        static::created(function($data){
            $data->code()->create();
        });

        static::deleting(function($q){
            $q->code()->delete();
        });
    }

    public static function generateMissingsCode()
    {
        return static::doesntHave('code')->get()->map(function($q){
            return $q->code()->create();
        });
    }
    
}