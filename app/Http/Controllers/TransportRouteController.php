<?php

namespace App\Http\Controllers;

use App\Models\TransportRoute;
use Illuminate\Http\Request;

class TransportRouteController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can('routes.view'), 403);

        $search = trim((string) $request->query('search'));

        $routes = TransportRoute::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('start_point', 'like', "%{$search}%")
                        ->orWhere('end_point', 'like', "%{$search}%");
                });
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.routes.index', compact('routes'));
    }

    public function create()
    {
        abort_unless(can('routes.create'), 403);

        return view('admin.routes.create');
    }

    public function store(Request $request)
    {
        abort_unless(can('routes.create'), 403);

        $request->validate([
            'code' => 'required|string|max:50|unique:routes,code',
            'name' => 'required|string|max:150',
            'route_type' => 'required|in:loop,round_trip,one_way',
            'start_point' => 'required|string|max:100',
            'end_point' => 'required|string|max:100',
            'distance_km' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string|max:500',
        ]);

        TransportRoute::create($request->all());

        return redirect()->route('routes.index')->with('success', 'Trayek berhasil ditambahkan!');
    }

    public function show(TransportRoute $route)
    {
        abort_unless(can('routes.view'), 403);

        return view('admin.routes.show', compact('route'));
    }

    public function edit(TransportRoute $route)
    {
        abort_unless(can('routes.edit'), 403);

        return view('admin.routes.edit', compact('route'));
    }

    public function update(Request $request, TransportRoute $route)
    {
        abort_unless(can('routes.edit'), 403);

        $request->validate([
            'code' => 'required|string|max:50|unique:routes,code,' . $route->id,
            'name' => 'required|string|max:150',
            'route_type' => 'required|in:loop,round_trip,one_way',
            'start_point' => 'required|string|max:100',
            'end_point' => 'required|string|max:100',
            'distance_km' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string|max:500',
        ]);

        $route->update($request->all());

        return redirect()->route('routes.index')->with('success', 'Trayek berhasil diperbarui!');
    }

    public function destroy(TransportRoute $route)
    {
        abort_unless(can('routes.delete'), 403);

        $route->delete();

        return redirect()->route('routes.index')->with('success', 'Trayek berhasil dihapus!');
    }
}
