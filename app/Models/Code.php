<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Str;
class Code extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [''];

    public function content()
    {
        return $this->morphTo('content');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($data) {
            if(empty($data->id))
            {
                do {
                    $id = Str::random(8);
                } while (static::find($id) != null);
                
                $data->id = $id;
            }
        });
    }

    public function regenerate()
    {
        $new = new Code([
            'content_id' => $this->content_id,
            'content_type' => $this->content_type
        ]);

        $this->delete();
        $new->save();

        return $new;
    }

    protected $keyType = 'string';
    public $incrementing = false;

}
