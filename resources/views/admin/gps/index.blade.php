@extends('layouts.app')

@section('title', 'GPS Tracking')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">GPS Tracking</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">GPS</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Trip</th>
                            <th>Kendaraan</th>
                            <th>Lokasi</th>
                            <th>Kecepatan</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trackings as $tracking)
                            <tr>
                                <td>{{ $trackings->firstItem() + $loop->index }}</td>
                                <td>{{ $tracking->trip?->trip_code ?? '-' }}</td>
                                <td>{{ $tracking->vehicle?->plate_number ?? '-' }}</td>
                                <td>{{ $tracking->latitude }}, {{ $tracking->longitude }}</td>
                                <td>{{ $tracking->speed_kmh ? $tracking->speed_kmh . ' km/h' : '-' }}</td>
                                <td>{{ $tracking->recorded_at?->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-6">Belum ada data GPS.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($trackings->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $trackings->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
