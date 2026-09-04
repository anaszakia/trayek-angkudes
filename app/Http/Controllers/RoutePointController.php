<?php

namespace App\Http\Controllers;

use App\Models\RoutePoint;
use App\Models\TransportRoute;
use Illuminate\Http\Request;

class RoutePointController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can('route_points.view'), 403);

        $search = trim((string) $request->query('search'));
        $points = RoutePoint::with('route')
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderBy('route_id')->orderBy('sequence')
            ->paginate(10)->withQueryString();

        return view('admin.route-points.index', compact('points'));
    }

    public function create()
    {
        abort_unless(can('route_points.create'), 403);
        $routes = TransportRoute::orderBy('code')->get();
        return view('admin.route-points.create', compact('routes'));
    }

    public function store(Request $request)
    {
        abort_unless(can('route_points.create'), 403);
        $data = $request->validate($this->rules());
        RoutePoint::create($data);
        return redirect()->route('route-points.index')->with('success', 'Titik trayek berhasil ditambahkan!');
    }

    public function show(RoutePoint $routePoint)
    {
        abort_unless(can('route_points.view'), 403);
        $routePoint->load('route');
        return view('admin.route-points.show', compact('routePoint'));
    }

    public function edit(RoutePoint $routePoint)
    {
        abort_unless(can('route_points.edit'), 403);
        $routes = TransportRoute::orderBy('code')->get();
        return view('admin.route-points.edit', compact('routePoint', 'routes'));
    }

    public function update(Request $request, RoutePoint $routePoint)
    {
        abort_unless(can('route_points.edit'), 403);
        $routePoint->update($request->validate($this->rules()));
        return redirect()->route('route-points.index')->with('success', 'Titik trayek berhasil diperbarui!');
    }

    public function destroy(RoutePoint $routePoint)
    {
        abort_unless(can('route_points.delete'), 403);
        $routePoint->delete();
        return redirect()->route('route-points.index')->with('success', 'Titik trayek berhasil dihapus!');
    }

    private function rules(): array
    {
        return [
            'route_id' => ['required', 'exists:routes,id'],
            'sequence' => ['required', 'integer', 'min:0'],
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_terminal' => ['required', 'boolean'],
        ];
    }
}
