<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'vehicle_id',
        'driver_id',
        'schedule_id',
        'trip_code',
        'started_at',
        'ended_at',
        'status',
        'total_passengers',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public static function hasActiveTripForDriverOrVehicle(?int $driverId = null, ?int $vehicleId = null, ?int $excludeId = null): bool
    {
        $query = self::query()
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->when($excludeId !== null, fn ($query) => $query->whereKeyNot($excludeId));

        if ($driverId !== null || $vehicleId !== null) {
            $query->where(function ($query) use ($driverId, $vehicleId) {
                if ($driverId !== null) {
                    $query->orWhere('driver_id', $driverId);
                }

                if ($vehicleId !== null) {
                    $query->orWhere('vehicle_id', $vehicleId);
                }
            });
        }

        return $query->exists();
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['scheduled', 'in_progress'], true);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(TransportSchedule::class, 'schedule_id');
    }

    public function gpsTrackings(): HasMany
    {
        return $this->hasMany(GpsTracking::class, 'trip_id');
    }
}
