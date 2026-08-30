<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can('drivers.view'), 403);

        $search = trim((string) $request->query('search'));

        $drivers = Driver::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('driver_code', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('license_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('driver_code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.drivers.index', compact('drivers'));
    }

    public function create()
    {
        abort_unless(can('drivers.create'), 403);

        $users = User::orderBy('name')->get();

        return view('admin.drivers.create', compact('users'));
    }

    public function store(Request $request)
    {
        abort_unless(can('drivers.create'), 403);

        $request->validate([
            'user_id' => 'required|exists:users,id|unique:drivers,user_id',
            'driver_code' => 'required|string|max:50|unique:drivers,driver_code',
            'nik' => 'nullable|string|max:20|unique:drivers,nik',
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50|unique:drivers,license_number',
            'license_type' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $data = $request->only([
            'user_id',
            'driver_code',
            'nik',
            'phone',
            'license_number',
            'license_type',
            'address',
            'status',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = minio_upload($request->file('photo'), 'drivers');
        }

        Driver::create($data);

        return redirect()->route('drivers.index')->with('success', 'Driver berhasil ditambahkan!');
    }

    public function show(Driver $driver)
    {
        abort_unless(can('drivers.view'), 403);

        $driver->load('user');

        return view('admin.drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        abort_unless(can('drivers.edit'), 403);

        $driver->load('user');
        $users = User::orderBy('name')->get();

        return view('admin.drivers.edit', compact('driver', 'users'));
    }

    public function update(Request $request, Driver $driver)
    {
        abort_unless(can('drivers.edit'), 403);

        $request->validate([
            'user_id' => 'required|exists:users,id|unique:drivers,user_id,' . $driver->id,
            'driver_code' => 'required|string|max:50|unique:drivers,driver_code,' . $driver->id,
            'nik' => 'nullable|string|max:20|unique:drivers,nik,' . $driver->id,
            'phone' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:50|unique:drivers,license_number,' . $driver->id,
            'license_type' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $data = $request->only([
            'user_id',
            'driver_code',
            'nik',
            'phone',
            'license_number',
            'license_type',
            'address',
            'status',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = minio_replace($driver->photo, $request->file('photo'), 'drivers');
        }

        if ($request->has('remove_photo') && $driver->photo) {
            minio_delete($driver->photo);
            $data['photo'] = null;
        }

        $driver->update($data);

        return redirect()->route('drivers.index')->with('success', 'Driver berhasil diperbarui!');
    }

    public function destroy(Driver $driver)
    {
        abort_unless(can('drivers.delete'), 403);

        if ($driver->photo) {
            minio_delete($driver->photo);
        }

        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver berhasil dihapus!');
    }
}
