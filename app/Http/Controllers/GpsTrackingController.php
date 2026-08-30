<?php

namespace App\Http\Controllers;

use App\Models\GpsTracking;
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tracking,
            ]);
        }

        return redirect()->route('gps.index')->with('success', 'Lokasi GPS berhasil dicatat!');
    }
}
