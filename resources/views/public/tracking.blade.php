<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Angkutan Desa | Pelacakan Langsung</title>
    @vite(['resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
    <style>
        :root { --ink:#16232b; --muted:#6c7a80; --paper:#f3f6f5; --white:#fff; --teal:#087f8c; --lime:#d5e95f; --line:#dbe5e2; }
        * { box-sizing:border-box; }
        body { margin:0; color:var(--ink); background:var(--paper); font-family:Georgia, 'Times New Roman', serif; }
        .topbar { display:flex; justify-content:space-between; align-items:center; padding:22px clamp(20px, 5vw, 72px); background:var(--ink); color:#fff; }
        .brand { font-size:clamp(1.2rem, 2vw, 1.7rem); font-weight:700; letter-spacing:.02em; }
        .brand span { color:var(--lime); }
        .live-pill { display:flex; align-items:center; gap:8px; font:600 .76rem Arial,sans-serif; text-transform:uppercase; letter-spacing:.12em; color:#d8f3ed; }
        .live-dot { width:9px; height:9px; border-radius:50%; background:#65df9b; box-shadow:0 0 0 5px #65df9b22; }
        .shell { max-width:1500px; margin:auto; padding:clamp(20px, 4vw, 52px); }
        .intro { display:flex; justify-content:space-between; align-items:end; gap:24px; margin-bottom:24px; }
        h1 { max-width:650px; margin:0; font-size:clamp(2rem, 4.5vw, 4.6rem); line-height:.98; letter-spacing:-.03em; }
        .intro p { max-width:330px; margin:0; color:var(--muted); font:1rem/1.5 Arial,sans-serif; }
        .dashboard { display:grid; grid-template-columns:minmax(0, 1.65fr) minmax(300px, .75fr); gap:18px; align-items:start; }
        .map-wrap { position:relative; min-height:600px; overflow:hidden; border-radius:20px; background:#dceae7; box-shadow:0 18px 45px #18373318; }
        #tracking-map { width:100%; height:clamp(500px, 65vh, 760px); }
        .map-status { position:absolute; z-index:500; top:16px; left:16px; display:flex; gap:8px; flex-wrap:wrap; }
        .status-chip { padding:9px 12px; border:1px solid #ffffffaa; border-radius:999px; background:#ffffffea; color:var(--ink); font:600 .75rem Arial,sans-serif; box-shadow:0 5px 15px #18373318; }
        .panel { display:flex; flex-direction:column; gap:14px; }
        .panel-card { padding:20px; border:1px solid var(--line); border-radius:16px; background:var(--white); box-shadow:0 10px 25px #1837330d; }
        .panel-card h2 { margin:0 0 14px; font-size:1.2rem; }
        .search { width:100%; padding:13px 14px; border:1px solid var(--line); border-radius:10px; color:var(--ink); font:1rem Arial,sans-serif; outline:none; }
        .route-list { display:flex; flex-direction:column; gap:8px; max-height:250px; overflow:auto; }
        .route-item { width:100%; padding:13px; border:1px solid var(--line); border-radius:10px; background:#fff; text-align:left; cursor:pointer; color:var(--ink); }
        .route-item.active { border-color:var(--teal); background:#e9f6f3; }
        .route-item strong { display:block; font:700 .95rem Arial,sans-serif; }
        .route-item small { display:block; margin-top:5px; color:var(--muted); font: .8rem Arial,sans-serif; }
        .route-heading { display:flex; justify-content:space-between; gap:12px; align-items:start; }
        .route-heading h3 { margin:0; font-size:1.5rem; }
        .route-code { color:var(--teal); font:700 .75rem Arial,sans-serif; }
        .route-meta { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin:15px 0; font: .83rem Arial,sans-serif; }
        .meta-label { display:block; color:var(--muted); margin-bottom:3px; }
        .detail-section { padding-top:14px; border-top:1px solid var(--line); }
        .detail-section h4 { margin:0 0 9px; font:700 .78rem Arial,sans-serif; text-transform:uppercase; letter-spacing:.1em; color:var(--muted); }
        .schedule, .fare { display:flex; justify-content:space-between; gap:12px; padding:7px 0; font: .86rem Arial,sans-serif; }
        .fare strong { color:var(--teal); }
        .empty { color:var(--muted); font: .86rem Arial,sans-serif; }
        .locate { width:100%; padding:12px; border:0; border-radius:10px; background:var(--ink); color:#fff; cursor:pointer; font:700 .9rem Arial,sans-serif; }
        .legend { display:flex; gap:15px; flex-wrap:wrap; color:var(--muted); font: .8rem Arial,sans-serif; }
        .legend span { display:flex; align-items:center; gap:6px; }
        .legend i { display:block; width:10px; height:10px; border-radius:50%; background:var(--teal); }
        .legend .car-dot { background:#ef754f; }
        .vehicle-icon { display:flex; align-items:center; justify-content:center; width:38px; height:38px; border:3px solid #fff; border-radius:50% 50% 50% 8px; background:#ef754f; color:#fff; box-shadow:0 4px 12px #18373355; transform:rotate(-45deg); }
        .vehicle-icon span { display:block; transform:rotate(45deg); font-size:20px; }
        .user-icon { display:flex; align-items:center; justify-content:center; width:20px; height:20px; border:4px solid #fff; border-radius:50%; background:var(--teal); box-shadow:0 2px 8px #18373355; }
        @media (max-width:900px) { .intro { display:block; } .intro p { margin-top:14px; } .dashboard { grid-template-columns:1fr; } .panel { order:-1; } .map-wrap { min-height:420px; } #tracking-map { height:58vh; min-height:420px; } }
        @media (max-width:520px) { .shell { padding:18px 14px 30px; } .topbar { padding:18px 16px; } .live-pill { font-size:.65rem; } .map-status { top:10px; left:10px; } .status-chip { padding:7px 9px; font-size:.68rem; } .panel-card { padding:16px; } }
    </style>
</head>
<body>
    <header class="topbar"><div class="brand">Angkutan <span>Desa</span></div><div class="live-pill"><i class="live-dot"></i> Pelacakan langsung</div></header>
    <main class="shell">
        <section class="intro"><div><h1>Temukan kendaraanmu di perjalanan.</h1></div><p>Jelajahi trayek, jadwal, dan tarif angkutan desa secara langsung dari lokasi Anda.</p></section>
        <section class="dashboard">
            <div class="map-wrap"><div class="map-status"><span class="status-chip" id="vehicle-count">0 kendaraan aktif</span><span class="status-chip" id="location-status">Lokasi belum dibaca</span></div><div id="tracking-map"></div></div>
            <aside class="panel">
                <div class="panel-card"><h2>Trayek tersedia</h2><input id="route-search" class="search" type="search" placeholder="Cari trayek..."><div id="route-list" class="route-list" style="margin-top:12px"></div></div>
                <div class="panel-card" id="route-detail"><div class="empty">Pilih trayek untuk melihat jadwal dan tarif.</div></div>
                <div class="panel-card"><button id="locate-button" class="locate">Gunakan lokasi saya</button><div class="legend" style="margin-top:14px"><span><i></i> Lokasi saya</span><span><i class="car-dot"></i> Kendaraan aktif</span></div></div>
            </aside>
        </section>
    </main>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        const map = L.map('tracking-map', { zoomControl: false }).setView([-7.75, 110.38], 11);
        L.control.zoom({ position: 'bottomright' }).addTo(map);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        const routeList = document.getElementById('route-list'), routeDetail = document.getElementById('route-detail'), vehicleCount = document.getElementById('vehicle-count'), locationStatus = document.getElementById('location-status'), vehicleLayer = L.layerGroup().addTo(map);
        let routes = [], vehicles = [], selectedRoute = null, userMarker = null, userWatch = null;
        const vehicleMarkers = new Map();
        const escapeHtml = (value) => String(value ?? '-').replace(/[&<>'"]/g, (char) => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char]));
        const formatRupiah = (value) => new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', maximumFractionDigits:0 }).format(value || 0);
        function carIcon(heading) { return L.divIcon({ className:'', html:`<div class="vehicle-icon" style="transform:rotate(${Number(heading || 0) - 45}deg)"><span>🚐</span></div>`, iconSize:[38,38], iconAnchor:[19,19] }); }
        function renderVehicles() { const visible = vehicles; const visibleIds = new Set(visible.map((item) => item.trip_id)); vehicleMarkers.forEach((marker, tripId) => { if (!visibleIds.has(tripId)) { vehicleLayer.removeLayer(marker); vehicleMarkers.delete(tripId); } }); visible.forEach((item) => { if (item.latitude === null || item.longitude === null) return; let marker = vehicleMarkers.get(item.trip_id); if (!marker) { marker = L.marker([item.latitude, item.longitude], { icon:carIcon(item.heading) }).addTo(vehicleLayer); marker.bindPopup(`<strong>${escapeHtml(item.vehicle || 'Kendaraan')}</strong><br>${escapeHtml(item.route || '')}<br>Kecepatan: <span data-speed>${escapeHtml(item.speed_kmh)}</span> km/jam<br>Pembaruan: <span data-time>${escapeHtml(item.recorded_at)}</span>`); vehicleMarkers.set(item.trip_id, marker); } else { marker.setLatLng([item.latitude, item.longitude]); marker.setIcon(carIcon(item.heading)); const popup = marker.getPopup(); if (popup) popup.setContent(`<strong>${escapeHtml(item.vehicle || 'Kendaraan')}</strong><br>${escapeHtml(item.route || '')}<br>Kecepatan: ${escapeHtml(item.speed_kmh)} km/jam<br>Pembaruan: ${escapeHtml(item.recorded_at)}`); } }); vehicleCount.textContent = `${visible.length} kendaraan aktif`; }
        function renderRoutes() { const query = document.getElementById('route-search').value.toLowerCase(); routeList.innerHTML = routes.filter((route) => `${route.code} ${route.name}`.toLowerCase().includes(query)).map((route) => `<button class="route-item ${selectedRoute?.id === route.id ? 'active' : ''}" data-route="${route.id}"><strong>${escapeHtml(route.code)} · ${escapeHtml(route.name)}</strong><small>${escapeHtml(route.start_point)} → ${escapeHtml(route.end_point)}${route.distance_km ? ` · ${route.distance_km} km` : ''}</small></button>`).join('') || '<div class="empty">Trayek tidak ditemukan.</div>'; routeList.querySelectorAll('[data-route]').forEach((button) => button.addEventListener('click', () => { selectedRoute = routes.find((route) => route.id === Number(button.dataset.route)); renderRoutes(); renderDetail(); renderVehicles(); drawRoute(); })); }
        function renderDetail() { if (!selectedRoute) { routeDetail.innerHTML = '<div class="empty">Pilih trayek untuk melihat jadwal dan tarif.</div>'; return; } const schedules = selectedRoute.schedules?.map((item) => `<div class="schedule"><span>${escapeHtml(item.day_of_week)}</span><strong>${escapeHtml(item.departure_time)} - ${escapeHtml(item.arrival_time)}</strong></div>`).join('') || '<div class="empty">Jadwal belum tersedia.</div>'; const fares = selectedRoute.fares?.map((item) => `<div class="fare"><span>${escapeHtml(item.name)}</span><strong>${formatRupiah(item.amount)}</strong></div>`).join('') || '<div class="empty">Tarif belum tersedia.</div>'; routeDetail.innerHTML = `<div class="route-heading"><h3>${escapeHtml(selectedRoute.name)}</h3><span class="route-code">${escapeHtml(selectedRoute.code)}</span></div><div class="route-meta"><div><span class="meta-label">Asal</span>${escapeHtml(selectedRoute.start_point)}</div><div><span class="meta-label">Tujuan</span>${escapeHtml(selectedRoute.end_point)}</div></div><div class="detail-section"><h4>Jadwal</h4>${schedules}</div><div class="detail-section"><h4>Tarif</h4>${fares}</div>`; }
        function drawRoute() { if (!selectedRoute) return; const points = (selectedRoute.points || []).filter((point) => point.latitude !== null && point.longitude !== null).sort((a,b) => a.sequence-b.sequence).map((point) => [point.latitude, point.longitude]); if (window.routeLine) map.removeLayer(window.routeLine); if (points.length > 1) { window.routeLine = L.polyline(points, { color:'#087f8c', weight:5, opacity:.85 }).addTo(map); map.fitBounds(window.routeLine.getBounds(), { padding:[30,30] }); } }
        function setUserLocation(position) { const point = [position.coords.latitude, position.coords.longitude]; if (!userMarker) { userMarker = L.marker(point, { icon:L.divIcon({ className:'', html:'<div class="user-icon"></div>', iconSize:[20,20], iconAnchor:[10,10] }) }).addTo(map).bindPopup('Lokasi Anda'); map.setView(point, 14); } else userMarker.setLatLng(point); locationStatus.textContent = 'Lokasi Anda aktif'; }
        function locateUser() { if (!navigator.geolocation) { locationStatus.textContent = 'GPS tidak didukung'; return; } locationStatus.textContent = 'Membaca lokasi...'; if (userWatch !== null) navigator.geolocation.clearWatch(userWatch); userWatch = navigator.geolocation.watchPosition(setUserLocation, () => { locationStatus.textContent = 'Izin lokasi ditolak'; }, { enableHighAccuracy:true, maximumAge:10000, timeout:15000 }); }
        function applyData(data) { routes = data.routes || []; vehicles = data.vehicles || []; if (!selectedRoute && routes.length) selectedRoute = routes.find((route) => vehicles.some((vehicle) => Number(vehicle.route_id) === Number(route.id))) || routes[0]; if (selectedRoute) selectedRoute = routes.find((route) => route.id === selectedRoute.id) || routes.find((route) => vehicles.some((vehicle) => Number(vehicle.route_id) === Number(route.id))) || routes[0]; renderRoutes(); renderDetail(); renderVehicles(); drawRoute(); }
        fetch('{{ route('tracking.data') }}').then((response) => response.json()).then(applyData).catch(() => { routeList.innerHTML = '<div class="empty">Data belum dapat dimuat.</div>'; });
        document.getElementById('route-search').addEventListener('input', renderRoutes); document.getElementById('locate-button').addEventListener('click', locateUser); locateUser();
        if (window.Echo) window.Echo.channel('vehicles').listen('.vehicle.location.updated', (item) => { const index = vehicles.findIndex((vehicle) => vehicle.trip_id === item.trip_id); if (index >= 0) vehicles[index] = { ...vehicles[index], ...item }; else vehicles.push(item); renderVehicles(); });
        setInterval(() => fetch('{{ route('tracking.data') }}').then((response) => response.json()).then(applyData).catch(() => {}), 30000);
    </script>
</body>
</html>
