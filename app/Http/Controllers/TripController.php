<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\TransportRoute;
use App\Models\TransportSchedule;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can('trips.view'), 403);

        $search = trim((string) $request->query('search'));

        $trips = Trip::with(['route', 'vehicle', 'driver', 'schedule'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('trip_code', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('started_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.trips.index', compact('trips'));
    }

    public function create()
    {
        abort_unless(can('trips.start'), 403);

        $routes = TransportRoute::orderBy('code')->get();
        $vehicles = Vehicle::orderBy('vehicle_code')->get();
        $drivers = Driver::with('user')->orderBy('driver_code')->get();
        $schedules = TransportSchedule::orderBy('schedule_code')->get();

        return view('admin.trips.create', compact('routes', 'vehicles', 'drivers', 'schedules'));
    }

    public function store(Request $request)
    {
        abort_unless(can('trips.start'), 403);

        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'schedule_id' => 'nullable|exists:schedules,id',
            'trip_code' => 'required|string|max:50|unique:trips,trip_code',
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'total_passengers' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        Trip::create($request->all());

        return redirect()->route('trips.index')->with('success', 'Trip berhasil dibuat!');
    }

    public function show(Trip $trip)
    {
        abort_unless(can('trips.view'), 403);

        $trip->load(['route', 'vehicle', 'driver.user', 'schedule']);

        return view('admin.trips.show', compact('trip'));
    }

    public function edit(Trip $trip)
    {
        abort_unless(can('trips.start'), 403);

        $trip->load(['route', 'vehicle', 'driver.user', 'schedule']);
        $routes = TransportRoute::orderBy('code')->get();
        $vehicles = Vehicle::orderBy('vehicle_code')->get();
        $drivers = Driver::with('user')->orderBy('driver_code')->get();
        $schedules = TransportSchedule::orderBy('schedule_code')->get();

        return view('admin.trips.edit', compact('trip', 'routes', 'vehicles', 'drivers', 'schedules'));
    }

    public function update(Request $request, Trip $trip)
    {
        abort_unless(can('trips.start'), 403);

        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'schedule_id' => 'nullable|exists:schedules,id',
            'trip_code' => 'required|string|max:50|unique:trips,trip_code,' . $trip->id,
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'total_passengers' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $trip->update($request->all());

        return redirect()->route('trips.index')->with('success', 'Trip berhasil diperbarui!');
    }

    public function destroy(Trip $trip)
    {
        abort_unless(can('trips.history'), 403);

        $trip->delete();

        return redirect()->route('trips.index')->with('success', 'Trip berhasil dihapus!');
    }
}
