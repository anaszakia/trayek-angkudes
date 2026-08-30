<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_code',
        'plate_number',
        'vehicle_type',
        'brand',
        'model',
        'color',
        'year',
        'capacity',
        'owner_name',
        'photo',
        'status',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(DriverVehicleAssignment::class);
    }

    public function drivers(): BelongsToMany
    {
        return $this->belongsToMany(Driver::class, 'driver_vehicle_assignments')
            ->withPivot(['id', 'started_at', 'ended_at', 'status'])
            ->withTimestamps();
    }
}
