@extends('layouts.app')

@section('title', 'Dasbor Pengemudi')

@push('head')
    <link rel="manifest" href="{{ asset('driver-manifest.json') }}">
    <meta name="theme-color" content="#16232b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-0">Dasbor Pengemudi</h4>
            <p class="text-muted mb-0">Halo, {{ $driver->user?->name ?? '-' }}</p>
        </div>
        <button id="install-driver-app" type="button" class="btn btn-outline-primary d-none"><i class="ti ti-download me-1"></i>Instal Aplikasi</button>
    </div>

    <div class="row g-4">
        <div class="col-xl-5">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <h5 class="mb-4">Kendaraan Aktif</h5>
                    @if($assignment)
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <span class="avatar avatar-lg bg-primary-subtle text-primary"><i class="ti ti-bus fs-3"></i></span>
                            <div>
                                <h5 class="mb-1">{{ $assignment->vehicle->plate_number }}</h5>
                                <p class="text-muted mb-0">{{ $assignment->vehicle->vehicle_code }} · {{ $assignment->vehicle->vehicle_type }}</p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">Belum ada kendaraan aktif yang ditugaskan.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="card card-lg h-100">
                <div class="card-body">
                    <h5 class="mb-4">Perjalanan</h5>
                    @if($currentTrip)
                        <div class="alert alert-success d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $currentTrip->route?->name }}</strong><br>
                                <small>{{ $currentTrip->trip_code }} · Dimulai {{ $currentTrip->started_at?->format('H:i') }}</small>
                            </div>
                            <span class="badge bg-success">Aktif</span>
                        </div>
                        <form action="{{ route('driver.trips.stop', $currentTrip) }}" method="POST">
                            @csrf
                            <button class="btn btn-danger"><i class="ti ti-player-stop me-1"></i>Selesaikan Perjalanan</button>
                        </form>
                        <div class="mt-4">
                            <div id="gps-status" class="text-muted">GPS belum diaktifkan.</div>
                            <button id="gps-start" type="button" class="btn btn-outline-primary mt-2"><i class="ti ti-current-location me-1"></i>Aktifkan GPS</button>
                        </div>
                    @elseif($assignment)
                        <form action="{{ route('driver.trips.start') }}" method="POST">
                            @csrf
                            <label class="form-label">Pilih Trayek</label>
                            <select name="route_id" class="form-select @error('route_id') is-invalid @enderror" required>
                                <option value="">Pilih trayek aktif</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->code }} - {{ $route->name }}</option>
                                @endforeach
                            </select>
                            @error('route_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <button class="btn btn-primary mt-3"><i class="ti ti-player-play me-1"></i>Mulai Perjalanan</button>
                        </form>
                    @else
                        <p class="text-muted mb-0">Tetapkan kendaraan terlebih dahulu sebelum memulai perjalanan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@if($currentTrip)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const button = document.getElementById('gps-start');
                const status = document.getElementById('gps-status');
                let watchId = null;
                let lastSent = 0;

                button.addEventListener('click', function () {
                    if (!navigator.geolocation) {
                        status.textContent = 'Perangkat tidak mendukung GPS.';
                        return;
                    }

                    button.disabled = true;
                    status.textContent = 'Mencari lokasi GPS...';
                    watchId = navigator.geolocation.watchPosition(function (position) {
                        const now = Date.now();
                        if (now - lastSent < 10000) return;
                        lastSent = now;
                        const coordinates = position.coords;

                        fetch('{{ route('driver.trips.location', $currentTrip) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                latitude: coordinates.latitude,
                                longitude: coordinates.longitude,
                                speed_kmh: coordinates.speed === null ? null : coordinates.speed * 3.6,
                                heading: coordinates.heading,
                                accuracy_m: coordinates.accuracy,
                                recorded_at: new Date(position.timestamp).toISOString()
                            })
                        }).then(function (response) {
                            if (!response.ok) throw new Error();
                            status.textContent = 'GPS aktif · lokasi terakhir terkirim ' + new Date().toLocaleTimeString();
                        }).catch(function () {
                            status.textContent = 'Gagal mengirim lokasi. Periksa koneksi internet.';
                        });
                    }, function () {
                        status.textContent = 'GPS tidak diizinkan atau belum tersedia.';
                    }, { enableHighAccuracy: true, maximumAge: 5000, timeout: 15000 });
                });

                window.addEventListener('beforeunload', function () {
                    if (watchId !== null) navigator.geolocation.clearWatch(watchId);
                });
            });
        </script>
    @endpush
@endif

@push('scripts')
    <script>
        let deferredInstallPrompt;
        window.addEventListener('beforeinstallprompt', function (event) {
            event.preventDefault();
            deferredInstallPrompt = event;
            document.getElementById('install-driver-app')?.classList.remove('d-none');
        });

        document.getElementById('install-driver-app')?.addEventListener('click', async function () {
            if (!deferredInstallPrompt) return;
            deferredInstallPrompt.prompt();
            await deferredInstallPrompt.userChoice;
            deferredInstallPrompt = null;
            this.classList.add('d-none');
        });

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('{{ asset('driver-sw.js') }}', { scope: '/' });
        }
    </script>
@endpush
