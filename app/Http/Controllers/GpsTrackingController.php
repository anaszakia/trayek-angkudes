<?php

namespace App\Http\Controllers;

use App\Events\VehicleLocationUpdated;
use App\Models\GpsTracking;
use App\Models\TransportRoute;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class GpsTrackingController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can('gps.view'), 403);

        $trackings = GpsTracking::with(['trip', 'vehicle'])
            ->orderByDesc('recorded_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.gps.index', compact('trackings'));
    }

    public function latest()
    {
        abort_unless(can('gps.view'), 403);

        $latest = GpsTracking::with(['trip.route', 'vehicle'])
            ->orderByDesc('recorded_at')
            ->get()
            ->groupBy('trip_id')
            ->map(fn ($items) => $items->first())
            ->values();

        return response()->json([
            'data' => $latest->map(function ($tracking) {
                return [
                    'trip_id' => $tracking->trip_id,
                    'trip_code' => $tracking->trip?->trip_code,
                    'vehicle' => $tracking->vehicle?->plate_number,
                    'latitude' => (float) $tracking->latitude,
                    'longitude' => (float) $tracking->longitude,
                    'speed_kmh' => $tracking->speed_kmh ? (float) $tracking->speed_kmh : null,
                    'recorded_at' => $tracking->recorded_at?->toISOString(),
                ];
            })->all(),
        ]);
    }

    public function map()
    {
        abort_unless(can('gps.view'), 403);

        $latest = GpsTracking::with(['trip.route', 'vehicle'])
            ->orderByDesc('recorded_at')
            ->get()
            ->groupBy('trip_id')
            ->map(fn ($items) => $items->first())
            ->values();

        return view('admin.gps.map', compact('latest'));
    }

    public function publicTracking()
    {
        $latest = GpsTracking::with(['trip.route', 'vehicle'])
            ->orderByDesc('recorded_at')
            ->get()
            ->groupBy('trip_id')
            ->map(fn ($items) => $items->first())
            ->values();

        return view('public.tracking', compact('latest'));
    }

    public function publicLatest()
    {
        $latest = GpsTracking::with(['trip.route', 'vehicle'])
            ->whereHas('trip', fn ($query) => $query->whereIn('status', ['scheduled', 'in_progress']))
            ->orderByDesc('recorded_at')
            ->get()
            ->groupBy('trip_id')
            ->map(fn ($items) => $items->first())
            ->values();

        return response()->json([
            'data' => $latest->map(function ($tracking) {
                return [
                    'trip_id' => $tracking->trip_id,
                    'trip_code' => $tracking->trip?->trip_code,
                    'route_id' => $tracking->trip?->route_id,
                    'route' => $tracking->trip?->route?->name,
                    'vehicle' => $tracking->vehicle?->plate_number,
                    'latitude' => (float) $tracking->latitude,
                    'longitude' => (float) $tracking->longitude,
                    'speed_kmh' => $tracking->speed_kmh ? (float) $tracking->speed_kmh : 0,
                    'recorded_at' => $tracking->recorded_at?->format('Y-m-d H:i:s'),
                    'heading' => $tracking->heading ? (float) $tracking->heading : null,
                ];
            })->all(),
        ]);
    }

    public function publicData()
    {
        $routes = TransportRoute::with([
            'points:id,route_id,sequence,name,latitude,longitude,is_terminal',
            'stops:id,route_id,sequence,name,latitude,longitude,is_active',
            'schedules:id,route_id,schedule_code,day_of_week,departure_time,arrival_time,frequency_minutes,status,description',
            'fares:id,route_id,fare_code,name,passenger_type,amount,currency,effective_from,effective_to,status,description',
        ])->where('status', 'active')->orderBy('code')->get();

        return response()->json([
            'routes' => $routes->map(fn (TransportRoute $route) => [
                'id' => $route->id,
                'code' => $route->code,
                'name' => $route->name,
                'route_type' => $route->route_type,
                'start_point' => $route->start_point,
                'end_point' => $route->end_point,
                'distance_km' => $route->distance_km ? (float) $route->distance_km : null,
                'description' => $route->description,
                'points' => $route->points->map(fn ($point) => [
                    'sequence' => $point->sequence, 'name' => $point->name,
                    'latitude' => (float) $point->latitude, 'longitude' => (float) $point->longitude,
                    'is_terminal' => (bool) $point->is_terminal,
                ])->values(),
                'stops' => $route->stops->where('is_active', true)->map(fn ($stop) => [
                    'sequence' => $stop->sequence, 'name' => $stop->name,
                    'latitude' => (float) $stop->latitude, 'longitude' => (float) $stop->longitude,
                ])->values(),
                'schedules' => $route->schedules->where('status', 'active')->map(fn ($schedule) => [
                    'day_of_week' => $schedule->day_of_week,
                    'departure_time' => substr($schedule->departure_time, 0, 5),
                    'arrival_time' => substr($schedule->arrival_time, 0, 5),
                    'frequency_minutes' => $schedule->frequency_minutes,
                    'description' => $schedule->description,
                ])->values(),
                'fares' => $route->fares->where('status', 'active')->map(fn ($fare) => [
                    'name' => $fare->name, 'passenger_type' => $fare->passenger_type,
                    'amount' => (float) $fare->amount, 'currency' => $fare->currency,
                    'description' => $fare->description,
                ])->values(),
            ])->values(),
            'vehicles' => $this->publicLatest()->getData(true)['data'],
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(can('gps.update'), 403);

        $request->validate([
            'trip_id' => ['required', 'exists:trips,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'speed_kmh' => ['nullable', 'numeric', 'min:0'],
            'heading' => ['nullable', 'numeric', 'min:0', 'max:360'],
            'accuracy_m' => ['nullable', 'numeric', 'min:0'],
            'recorded_at' => ['required', 'date'],
        ]);

        $trip = Trip::findOrFail($request->trip_id);

        if (! $trip->isActive()) {
            return back()->withErrors(['trip_id' => 'Trip ini tidak aktif, jadi lokasi GPS tidak dapat diterima.'])->withInput();
        }

        $vehicleId = $request->vehicle_id ?? $trip->vehicle_id;

        if ($vehicleId && $trip->vehicle_id && (int) $vehicleId !== (int) $trip->vehicle_id) {
            return back()->withErrors(['vehicle_id' => 'Vehicle yang dikirim tidak sesuai dengan trip aktif.'])->withInput();
        }

        $tracking = GpsTracking::create([
            'trip_id' => $trip->id,
            'vehicle_id' => $vehicleId,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'speed_kmh' => $request->speed_kmh,
            'heading' => $request->heading,
            'accuracy_m' => $request->accuracy_m,
            'recorded_at' => $request->recorded_at,
        ]);
        VehicleLocationUpdated::dispatch($tracking);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tracking,
            ]);
        }

        return redirect()->route('gps.index')->with('success', 'Lokasi GPS berhasil dicatat!');
    }
}
