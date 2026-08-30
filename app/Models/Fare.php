<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fare extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'fare_code',
        'name',
        'passenger_type',
        'amount',
        'currency',
        'effective_from',
        'effective_to',
        'status',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function route(): BelongsTo
    {
        return $this->belongsTo(TransportRoute::class, 'route_id');
    }
}
