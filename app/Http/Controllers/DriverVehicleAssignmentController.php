<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\DriverVehicleAssignment;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DriverVehicleAssignmentController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can('assignments.view'), 403);

        $search = trim((string) $request->query('search'));

        $assignments = DriverVehicleAssignment::with(['driver.user', 'vehicle'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('driver.user', function ($driverQuery) use ($search) {
                        $driverQuery->where('name', 'like', "%{$search}%");
                    })->orWhereHas('vehicle', function ($vehicleQuery) use ($search) {
                        $vehicleQuery->where('plate_number', 'like', "%{$search}%")
                            ->orWhere('vehicle_code', 'like', "%{$search}%");
                    });
                });
            })
            ->orderByDesc('started_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        abort_unless(can('assignments.create'), 403);

        $drivers = Driver::with('user')->orderBy('driver_code')->get();
        $vehicles = Vehicle::orderBy('vehicle_code')->get();

        return view('admin.assignments.create', compact('drivers', 'vehicles'));
    }

    public function store(Request $request)
    {
        abort_unless(can('assignments.create'), 403);

        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'status' => 'required|in:active,ended,cancelled',
        ]);

        $activeExists = DriverVehicleAssignment::where('vehicle_id', $request->vehicle_id)
            ->where('status', 'active')
            ->when($request->status === 'active', fn($query) => $query)
            ->exists();

        if ($request->status === 'active' && $activeExists) {
            return back()->withErrors(['vehicle_id' => 'Kendaraan ini sudah memiliki assignment aktif.'])->withInput();
        }

        $driverActiveExists = DriverVehicleAssignment::where('driver_id', $request->driver_id)
            ->where('status', 'active')
            ->exists();

        if ($request->status === 'active' && $driverActiveExists) {
            return back()->withErrors(['driver_id' => 'Driver ini sudah memiliki kendaraan aktif.'])->withInput();
        }

        DriverVehicleAssignment::create([
            'driver_id' => $request->driver_id,
            'vehicle_id' => $request->vehicle_id,
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
            'status' => $request->status,
        ]);

        return redirect()->route('assignments.index')->with('success', 'Assignment berhasil dibuat!');
    }

    public function show(DriverVehicleAssignment $assignment)
    {
        abort_unless(can('assignments.view'), 403);

        $assignment->load(['driver.user', 'vehicle']);

        return view('admin.assignments.show', compact('assignment'));
    }

    public function edit(DriverVehicleAssignment $assignment)
    {
        abort_unless(can('assignments.edit'), 403);

        $assignment->load(['driver.user', 'vehicle']);
        $drivers = Driver::with('user')->orderBy('driver_code')->get();
        $vehicles = Vehicle::orderBy('vehicle_code')->get();

        return view('admin.assignments.edit', compact('assignment', 'drivers', 'vehicles'));
    }

    public function update(Request $request, DriverVehicleAssignment $assignment)
    {
        abort_unless(can('assignments.edit'), 403);

        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'status' => 'required|in:active,ended,cancelled',
        ]);

        if ($request->status === 'active') {
            $vehicleActive = DriverVehicleAssignment::where('vehicle_id', $request->vehicle_id)
                ->where('status', 'active')
                ->whereKeyNot($assignment->id)
                ->exists();

            if ($vehicleActive) {
                return back()->withErrors(['vehicle_id' => 'Kendaraan ini sudah memiliki assignment aktif.'])->withInput();
            }

            $driverActive = DriverVehicleAssignment::where('driver_id', $request->driver_id)
                ->where('status', 'active')
                ->whereKeyNot($assignment->id)
                ->exists();

            if ($driverActive) {
                return back()->withErrors(['driver_id' => 'Driver ini sudah memiliki kendaraan aktif.'])->withInput();
            }
        }

        $assignment->update([
            'driver_id' => $request->driver_id,
            'vehicle_id' => $request->vehicle_id,
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
            'status' => $request->status,
        ]);

        return redirect()->route('assignments.index')->with('success', 'Assignment berhasil diperbarui!');
    }

    public function destroy(DriverVehicleAssignment $assignment)
    {
        abort_unless(can('assignments.delete'), 403);

        $assignment->delete();

        return redirect()->route('assignments.index')->with('success', 'Assignment berhasil dihapus!');
    }
}
