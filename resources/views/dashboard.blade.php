@php
    $activeTrips = \App\Models\Trip::whereIn('status', ['scheduled', 'in_progress'])->count();
    $activeVehicles = \App\Models\Vehicle::where('status', 'active')->count();
    $totalDrivers = \App\Models\Driver::count();
    $totalRoutes = \App\Models\TransportRoute::count();
    $latestTrips = \App\Models\Trip::with(['route', 'driver.user', 'vehicle'])
        ->orderByDesc('started_at')
        ->limit(5)
        ->get();
@endphp

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1">Dashboard Operasional</h4>
            <p class="text-muted mb-0">Selamat datang, {{ session('user_name') }}.</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-6 mb-6">
        <div class="col">
            <div class="card card-lg shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Trip Aktif</span>
                        <span class="badge bg-primary-subtle text-primary-emphasis"><i class="ti ti-route me-1"></i> Operasi</span>
                    </div>
                    <div class="fs-2 fw-bold">{{ $activeTrips }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Kendaraan Aktif</span>
                        <span class="badge bg-success-subtle text-success-emphasis"><i class="ti ti-bus me-1"></i> Ready</span>
                    </div>
                    <div class="fs-2 fw-bold">{{ $activeVehicles }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Jumlah Driver</span>
                        <span class="badge bg-warning-subtle text-warning-emphasis"><i class="ti ti-user me-1"></i> SDM</span>
                    </div>
                    <div class="fs-2 fw-bold">{{ $totalDrivers }}</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card card-lg shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Trayek</span>
                        <span class="badge bg-info-subtle text-info-emphasis"><i class="ti ti-map me-1"></i> Rute</span>
                    </div>
                    <div class="fs-2 fw-bold">{{ $totalRoutes }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-6 mb-6">
        <div class="col-xl-8">
            <div class="card card-lg shadow-sm border-0">
                <div class="card-header bg-transparent border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Trip Terbaru</h5>
                        <a href="{{ route('trips.index') }}" class="btn btn-sm btn-white">Lihat semua</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Trip</th>
                                    <th>Trayek</th>
                                    <th>Driver</th>
                                    <th>Kendaraan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestTrips as $trip)
                                    <tr>
                                        <td>{{ $trip->trip_code }}</td>
                                        <td>{{ $trip->route?->name ?? '-' }}</td>
                                        <td>{{ $trip->driver?->user?->name ?? '-' }}</td>
                                        <td>{{ $trip->vehicle?->plate_number ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $trip->status === 'in_progress' ? 'bg-primary-subtle text-primary-emphasis' : 'bg-secondary-subtle text-secondary-emphasis' }}">
                                                {{ ucfirst(str_replace('_', ' ', $trip->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">Belum ada data trip.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card card-lg shadow-sm border-0 h-100">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h5 class="mb-0">Status Operasional</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Trip aktif</span>
                        <strong>{{ $activeTrips }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Kendaraan aktif</span>
                        <strong>{{ $activeVehicles }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Driver terdaftar</span>
                        <strong>{{ $totalDrivers }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Trayek tersedia</span>
                        <strong>{{ $totalRoutes }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
