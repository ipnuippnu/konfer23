<?php

namespace App\Models;

use App\Traits\Uuids;
use App\Types\EventParams;
use App\Types\EventTargetType;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, Uuids, SoftDeletes;

    protected $guarded = ['id'];

    public $casts = [
        'target_ids' => 'array',
        'event_start' => 'datetime',
        'event_end' => 'datetime',
        'params' => AsEnumCollection::class.':'.EventParams::class,
        'target_type' => EventTargetType::class
    ];

    public function members()
    {
        return match($this->target_type) {
            EventTargetType::DELEGATORS => $this->morphedByMany(Delegator::class, 'eventable')->withTimestamps(),
            EventTargetType::PARTICIPANTS => $this->morphedByMany(Participant::class, 'eventable')->withTimestamps(),
            EventTargetType::PAYMENTS => $this->morphedByMany(Payment::class, 'eventable')->withTimestamps(),
            EventTargetType::GUESTS => $this->morphedByMany(Guest::class, 'eventable')->withTimestamps(),
            default => null
        };
    }
}
