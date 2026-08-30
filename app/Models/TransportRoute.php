<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRoute extends Model
{
    use HasFactory;

    protected $table = 'routes';

    protected $fillable = [
        'code',
        'name',
        'route_type',
        'start_point',
        'end_point',
        'distance_km',
        'status',
        'description',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
    ];

    public function points(): HasMany
    {
        return $this->hasMany(RoutePoint::class, 'route_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(RouteStop::class, 'route_id');
    }
}
