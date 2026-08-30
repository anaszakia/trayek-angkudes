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

    public function store(Request $request)
    {
        abort_unless(can('gps.update'), 403);

        $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed_kmh' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|min:0|max:360',
            'accuracy_m' => 'nullable|numeric|min:0',
            'recorded_at' => 'required|date',
        ]);

        GpsTracking::create($request->all());

        return redirect()->route('gps.index')->with('success', 'Lokasi GPS berhasil dicatat!');
    }
}
