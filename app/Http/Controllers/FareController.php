<?php

namespace App\Http\Controllers;

use App\Models\Fare;
use App\Models\TransportRoute;
use Illuminate\Http\Request;

class FareController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(can('fares.view'), 403);

        $search = trim((string) $request->query('search'));

        $fares = Fare::with('route')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('fare_code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('passenger_type', 'like', "%{$search}%")
                        ->orWhereHas('route', function ($routeQuery) use ($search) {
                            $routeQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('fare_code')
            ->paginate(10)
            ->withQueryString();

        return view('admin.fares.index', compact('fares'));
    }

    public function create()
    {
        abort_unless(can('fares.create'), 403);

        $routes = TransportRoute::orderBy('code')->get();

        return view('admin.fares.create', compact('routes'));
    }

    public function store(Request $request)
    {
        abort_unless(can('fares.create'), 403);

        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'fare_code' => 'required|string|max:50|unique:fares,fare_code',
            'name' => 'required|string|max:100',
            'passenger_type' => 'required|in:general,student,senior,disabled,children',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:500',
        ]);

        Fare::create($request->all());

        return redirect()->route('fares.index')->with('success', 'Tarif berhasil ditambahkan!');
    }

    public function show(Fare $fare)
    {
        abort_unless(can('fares.view'), 403);

        $fare->load('route');

        return view('admin.fares.show', compact('fare'));
    }

    public function edit(Fare $fare)
    {
        abort_unless(can('fares.edit'), 403);

        $fare->load('route');
        $routes = TransportRoute::orderBy('code')->get();

        return view('admin.fares.edit', compact('fare', 'routes'));
    }

    public function update(Request $request, Fare $fare)
    {
        abort_unless(can('fares.edit'), 403);

        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'fare_code' => 'required|string|max:50|unique:fares,fare_code,' . $fare->id,
            'name' => 'required|string|max:100',
            'passenger_type' => 'required|in:general,student,senior,disabled,children',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:500',
        ]);

        $fare->update($request->all());

        return redirect()->route('fares.index')->with('success', 'Tarif berhasil diperbarui!');
    }

    public function destroy(Fare $fare)
    {
        abort_unless(can('fares.delete'), 403);

        $fare->delete();

        return redirect()->route('fares.index')->with('success', 'Tarif berhasil dihapus!');
    }
}
