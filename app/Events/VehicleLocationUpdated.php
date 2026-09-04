<?php

namespace App\Events;

use App\Models\GpsTracking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VehicleLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public GpsTracking $tracking)
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('vehicles')];
    }

    public function broadcastAs(): string
    {
        return 'vehicle.location.updated';
    }

    public function broadcastWith(): array
    {
        $tracking = $this->tracking->loadMissing(['trip.route', 'vehicle']);

        return [
            'trip_id' => $tracking->trip_id,
            'trip_code' => $tracking->trip?->trip_code,
            'route' => $tracking->trip?->route?->name,
            'vehicle' => $tracking->vehicle?->plate_number,
            'latitude' => (float) $tracking->latitude,
            'longitude' => (float) $tracking->longitude,
            'speed_kmh' => $tracking->speed_kmh ? (float) $tracking->speed_kmh : 0,
            'heading' => $tracking->heading ? (float) $tracking->heading : null,
            'recorded_at' => $tracking->recorded_at?->toISOString(),
        ];
    }
}
