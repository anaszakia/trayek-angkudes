<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GpsTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'vehicle_id',
        'latitude',
        'longitude',
        'speed_kmh',
        'heading',
        'accuracy_m',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'speed_kmh' => 'decimal:2',
        'heading' => 'decimal:2',
        'accuracy_m' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
