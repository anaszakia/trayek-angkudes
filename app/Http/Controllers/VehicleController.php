<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can('vehicles.view'), 403);

        $search = trim((string) $request->query('search'));

        $vehicles = Vehicle::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('vehicle_code', 'like', "%{$search}%")
                        ->orWhere('plate_number', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('vehicle_code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        abort_unless(can('vehicles.create'), 403);

        return view('admin.vehicles.create');
    }

    public function store(Request $request)
    {
        abort_unless(can('vehicles.create'), 403);

        $request->validate([
            'vehicle_code' => 'required|string|max:50|unique:vehicles,vehicle_code',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'vehicle_type' => 'required|string|max:50',
            'brand' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'year' => 'nullable|digits:4',
            'capacity' => 'nullable|integer|min:1',
            'owner_name' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $data = $request->only([
            'vehicle_code',
            'plate_number',
            'vehicle_type',
            'brand',
            'model',
            'color',
            'year',
            'capacity',
            'owner_name',
            'status',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = minio_upload($request->file('photo'), 'vehicles');
        }

        Vehicle::create($data);

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan!');
    }

    public function show(Vehicle $vehicle)
    {
        abort_unless(can('vehicles.view'), 403);

        return view('admin.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        abort_unless(can('vehicles.edit'), 403);

        return view('admin.vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        abort_unless(can('vehicles.edit'), 403);

        $request->validate([
            'vehicle_code' => 'required|string|max:50|unique:vehicles,vehicle_code,' . $vehicle->id,
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
            'vehicle_type' => 'required|string|max:50',
            'brand' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'year' => 'nullable|digits:4',
            'capacity' => 'nullable|integer|min:1',
            'owner_name' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $data = $request->only([
            'vehicle_code',
            'plate_number',
            'vehicle_type',
            'brand',
            'model',
            'color',
            'year',
            'capacity',
            'owner_name',
            'status',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = minio_replace($vehicle->photo, $request->file('photo'), 'vehicles');
        }

        if ($request->has('remove_photo') && $vehicle->photo) {
            minio_delete($vehicle->photo);
            $data['photo'] = null;
        }

        $vehicle->update($data);

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil diperbarui!');
    }

    public function destroy(Vehicle $vehicle)
    {
        abort_unless(can('vehicles.delete'), 403);

        if ($vehicle->photo) {
            minio_delete($vehicle->photo);
        }

        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Kendaraan berhasil dihapus!');
    }
}
