<?php

namespace App\Http\Controllers;

use App\Models\RouteStop;
use App\Models\TransportRoute;
use Illuminate\Http\Request;

class RouteStopController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can('route_stops.view'), 403);

        $search = trim((string) $request->query('search'));
        $stops = RouteStop::with('route')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('route_id')->orderBy('sequence')
            ->paginate(10)->withQueryString();

        return view('admin.route-stops.index', compact('stops'));
    }

    public function create()
    {
        abort_unless(can('route_stops.create'), 403);
        $routes = TransportRoute::orderBy('code')->get();
        return view('admin.route-stops.create', compact('routes'));
    }

    public function store(Request $request)
    {
        abort_unless(can('route_stops.create'), 403);
        RouteStop::create($request->validate($this->rules()));
        return redirect()->route('route-stops.index')->with('success', 'Halte berhasil ditambahkan!');
    }

    public function show(RouteStop $routeStop)
    {
        abort_unless(can('route_stops.view'), 403);
        $routeStop->load('route');
        return view('admin.route-stops.show', compact('routeStop'));
    }

    public function edit(RouteStop $routeStop)
    {
        abort_unless(can('route_stops.edit'), 403);
        $routes = TransportRoute::orderBy('code')->get();
        return view('admin.route-stops.edit', compact('routeStop', 'routes'));
    }

    public function update(Request $request, RouteStop $routeStop)
    {
        abort_unless(can('route_stops.edit'), 403);
        $routeStop->update($request->validate($this->rules()));
        return redirect()->route('route-stops.index')->with('success', 'Halte berhasil diperbarui!');
    }

    public function destroy(RouteStop $routeStop)
    {
        abort_unless(can('route_stops.delete'), 403);
        $routeStop->delete();
        return redirect()->route('route-stops.index')->with('success', 'Halte berhasil dihapus!');
    }

    private function rules(): array
    {
        return [
            'route_id' => ['required', 'exists:routes,id'],
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'sequence' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
