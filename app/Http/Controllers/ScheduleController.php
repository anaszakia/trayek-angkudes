<?php

namespace App\Http\Controllers;

use App\Models\TransportRoute;
use App\Models\TransportSchedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can('schedules.view'), 403);

        $search = trim((string) $request->query('search'));

        $schedules = TransportSchedule::with('route')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('schedule_code', 'like', "%{$search}%")
                        ->orWhere('day_of_week', 'like', "%{$search}%")
                        ->orWhereHas('route', function ($routeQuery) use ($search) {
                            $routeQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('schedule_code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        abort_unless(can('schedules.create'), 403);

        $routes = TransportRoute::orderBy('code')->get();

        return view('admin.schedules.create', compact('routes'));
    }

    public function store(Request $request)
    {
        abort_unless(can('schedules.create'), 403);

        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'schedule_code' => 'required|string|max:50|unique:schedules,schedule_code',
            'day_of_week' => 'required|string|max:50',
            'departure_time' => 'required|date_format:H:i:s',
            'arrival_time' => 'required|date_format:H:i:s|after:departure_time',
            'frequency_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:500',
        ]);

        TransportSchedule::create($request->all());

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function show(TransportSchedule $schedule)
    {
        abort_unless(can('schedules.view'), 403);

        $schedule->load('route');

        return view('admin.schedules.show', compact('schedule'));
    }

    public function edit(TransportSchedule $schedule)
    {
        abort_unless(can('schedules.edit'), 403);

        $schedule->load('route');
        $routes = TransportRoute::orderBy('code')->get();

        return view('admin.schedules.edit', compact('schedule', 'routes'));
    }

    public function update(Request $request, TransportSchedule $schedule)
    {
        abort_unless(can('schedules.edit'), 403);

        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'schedule_code' => 'required|string|max:50|unique:schedules,schedule_code,' . $schedule->id,
            'day_of_week' => 'required|string|max:50',
            'departure_time' => 'required|date_format:H:i:s',
            'arrival_time' => 'required|date_format:H:i:s|after:departure_time',
            'frequency_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:500',
        ]);

        $schedule->update($request->all());

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy(TransportSchedule $schedule)
    {
        abort_unless(can('schedules.delete'), 403);

        $schedule->delete();

        return redirect()->route('schedules.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}
