@extends('layouts.app')

@section('title', 'Live Map')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #live-map { height: 70vh; width: 100%; border-radius: 16px; }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Live Map</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('gps.index') }}">GPS</a></li>
                    <li class="breadcrumb-item active">Live Map</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">
            <div id="live-map"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const map = L.map('live-map').setView([-7.75, 110.38], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markers = {!! json_encode($latest->map(function ($tracking) {
            return [
                'trip_id' => $tracking->trip_id,
                'trip_code' => $tracking->trip?->trip_code,
                'vehicle' => $tracking->vehicle?->plate_number,
                'latitude' => (float) $tracking->latitude,
                'longitude' => (float) $tracking->longitude,
                'speed_kmh' => $tracking->speed_kmh ? (float) $tracking->speed_kmh : 0,
                'recorded_at' => $tracking->recorded_at?->format('Y-m-d H:i:s'),
            ];
        })->all(), JSON_THROW_ON_ERROR) !!};

        markers.forEach((item) => {
            if (!item.latitude || !item.longitude) return;

            const marker = L.marker([item.latitude, item.longitude]).addTo(map);
            marker.bindPopup(`
                <div>
                    <strong>${item.trip_code ?? 'Trip'}</strong><br>
                    Kendaraan: ${item.vehicle ?? '-'}<br>
                    Kecepatan: ${item.speed_kmh ?? 0} km/h<br>
                    Waktu: ${item.recorded_at ?? '-'}
                </div>
            `);
        });
    </script>
@endpush
