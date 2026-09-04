<?php

namespace App\Http\Controllers;

use App\Events\VehicleLocationUpdated;
use App\Models\Driver;
use App\Models\DriverVehicleAssignment;
use App\Models\GpsTracking;
use App\Models\TransportRoute;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DriverDashboardController extends Controller
{
    public function dashboard()
    {
        abort_unless(can('trips.view'), 403);

        $driver = $this->driver();
        $assignment = DriverVehicleAssignment::with('vehicle')
            ->where('driver_id', $driver->id)
            ->where('status', 'active')
            ->latest('started_at')
            ->first();
        $routes = TransportRoute::where('status', 'active')->orderBy('code')->get();
        $currentTrip = Trip::with('route')
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->latest('created_at')
            ->first();

        return view('driver.dashboard', compact('driver', 'assignment', 'routes', 'currentTrip'));
    }

    public function start(Request $request)
    {
        abort_unless(can('trips.start'), 403);

        $data = $request->validate([
            'route_id' => ['required', 'exists:routes,id'],
        ]);
        $driver = $this->driver();
        $assignment = DriverVehicleAssignment::where('driver_id', $driver->id)
            ->where('status', 'active')->latest('started_at')->first();

        if (!$assignment) {
            return back()->withErrors(['route_id' => 'Belum ada kendaraan aktif yang ditugaskan.'])->withInput();
        }

        if (Trip::hasActiveTripForDriverOrVehicle($driver->id, $assignment->vehicle_id)) {
            return back()->withErrors(['route_id' => 'Driver atau kendaraan masih memiliki trip aktif.'])->withInput();
        }

        DB::transaction(function () use ($data, $driver, $assignment) {
            Trip::create([
                'route_id' => $data['route_id'],
                'driver_id' => $driver->id,
                'vehicle_id' => $assignment->vehicle_id,
                'trip_code' => 'TRIP-' . strtoupper(Str::random(12)),
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
        });

        return redirect()->route('driver.dashboard')->with('success', 'Perjalanan berhasil dimulai. Aktifkan GPS untuk mengirim lokasi.');
    }

    public function stop(Trip $trip)
    {
        abort_unless(can('trips.stop'), 403);
        $this->ensureOwnedTrip($trip);

        if (!$trip->isActive()) {
            return back()->withErrors(['trip' => 'Trip ini sudah tidak aktif.']);
        }

        $trip->update(['status' => 'completed', 'ended_at' => now()]);

        return redirect()->route('driver.dashboard')->with('success', 'Perjalanan berhasil diselesaikan.');
    }

    public function location(Request $request, Trip $trip)
    {
        abort_unless(can('gps.update'), 403);
        $this->ensureOwnedTrip($trip);

        if (!$trip->isActive()) {
            return response()->json(['message' => 'Trip ini sudah tidak aktif.'], 409);
        }

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'speed_kmh' => ['nullable', 'numeric', 'min:0'],
            'heading' => ['nullable', 'numeric', 'min:0', 'max:360'],
            'accuracy_m' => ['nullable', 'numeric', 'min:0'],
            'recorded_at' => ['required', 'date', 'after:' . now()->subMinutes(10)->toDateTimeString(), 'before:' . now()->addMinutes(5)->toDateTimeString()],
        ]);

        $tracking = GpsTracking::create($data + [
            'trip_id' => $trip->id,
            'vehicle_id' => $trip->vehicle_id,
        ]);
        VehicleLocationUpdated::dispatch($tracking);

        return response()->json(['success' => true, 'data' => $tracking]);
    }

    private function driver(): Driver
    {
        return Driver::where('user_id', session('user_id'))->firstOrFail();
    }

    private function ensureOwnedTrip(Trip $trip): void
    {
        abort_unless((int) $trip->driver_id === (int) $this->driver()->id, 403);
    }
}
