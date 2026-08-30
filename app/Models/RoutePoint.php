<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoutePoint extends Model
{
    use HasFactory;

    protected $table = 'route_points';

    protected $fillable = [
        'route_id',
        'sequence',
        'name',
        'latitude',
        'longitude',
        'is_terminal',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_terminal' => 'boolean',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }
}
