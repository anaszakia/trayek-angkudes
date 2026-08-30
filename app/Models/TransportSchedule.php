<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportSchedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'route_id',
        'schedule_code',
        'day_of_week',
        'departure_time',
        'arrival_time',
        'frequency_minutes',
        'status',
        'description',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'schedule_id');
    }
}
