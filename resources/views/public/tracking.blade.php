<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Tracking</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @vite(['resources/js/app.js'])
    <style>
        body { margin: 0; font-family: Arial, sans-serif; background: #f5f7fb; }
        .topbar { background: #0f172a; color: white; padding: 18px 24px; font-weight: 700; }
        .container { padding: 24px; }
        #tracking-map { height: 72vh; width: 100%; border-radius: 18px; border: 1px solid #dbe2ea; }
        .legend { display: flex; gap: 14px; align-items: center; margin-top: 16px; flex-wrap: wrap; }
        .legend-item { display: flex; align-items: center; gap: 8px; }
        .dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; }
    </style>
</head>
<body>
    <div class="topbar">Peta Tracking Angkutan Desa</div>

    <div class="container">
        <div id="tracking-map"></div>

        <div class="legend">
            <div class="legend-item"><span class="dot" style="background: #16a34a;"></span> Kendaraan aktif</div>
            <div class="legend-item"><span class="dot" style="background: #ef4444;"></span> Data terakhir</div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        const map = L.map('tracking-map').setView([-7.75, 110.38], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markers = {!! json_encode($latest->map(function ($tracking) {
            return [
                'trip_id' => $tracking->trip_id,
                'trip_code' => $tracking->trip?->trip_code,
                'route' => $tracking->trip?->route?->name,
                'vehicle' => $tracking->vehicle?->plate_number,
                'latitude' => (float) $tracking->latitude,
                'longitude' => (float) $tracking->longitude,
                'speed_kmh' => $tracking->speed_kmh ? (float) $tracking->speed_kmh : 0,
                'recorded_at' => $tracking->recorded_at?->format('Y-m-d H:i:s'),
            ];
        })->all(), JSON_THROW_ON_ERROR) !!};

        const markerLayer = L.layerGroup().addTo(map);
        const liveMarkers = new Map(markers.map((item) => [item.trip_id, item]));

        function renderMarkers(data) {
            markerLayer.clearLayers();

            data.forEach((item) => {
                if (!item.latitude || !item.longitude) return;

                const marker = L.marker([item.latitude, item.longitude]).addTo(markerLayer);
                marker.bindPopup(`
                    <div>
                        <strong>${item.trip_code ?? 'Trip'}</strong><br>
                        Route: ${item.route ?? '-'}<br>
                        Kendaraan: ${item.vehicle ?? '-'}<br>
                        Kecepatan: ${item.speed_kmh ?? 0} km/h<br>
                        Waktu: ${item.recorded_at ?? '-'}
                    </div>
                `);
            });

            if (data.length) {
                const bounds = L.latLngBounds(data.filter(item => item.latitude && item.longitude).map(item => [item.latitude, item.longitude]));
                map.fitBounds(bounds, { padding: [32, 32] });
            }
        }

        renderMarkers(markers);

        if (window.Echo) {
            window.Echo.channel('vehicles').listen('.vehicle.location.updated', (item) => {
                liveMarkers.set(item.trip_id, item);
                renderMarkers(Array.from(liveMarkers.values()));
            });
        }

        setInterval(() => {
            fetch('{{ route('tracking.latest') }}')
                .then(res => res.json())
                .then(data => {
                    if (data && data.data) {
                        liveMarkers.clear();
                        data.data.forEach((item) => liveMarkers.set(item.trip_id, item));
                        renderMarkers(data.data);
                    }
                })
                .catch(() => {});
        }, 15000);
    </script>
</body>
</html>
