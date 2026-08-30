# PRD — Sistem Monitoring Trayek Angkutan Desa

**Versi:** 1.0  
**Tanggal:** 30 Agustus 2026  
**Status:** Draft Acuan Pengembangan  
**Framework:** Laravel 13

---

## 1. Ringkasan Produk

Sistem Monitoring Trayek Angkutan Desa adalah aplikasi berbasis web/PWA untuk membantu Dinas Perhubungan mengelola data trayek, angkutan, sopir, tarif, jadwal, serta memantau posisi angkutan secara real-time menggunakan GPS.

Sistem memiliki tiga role utama:

1. **Super Admin** — pengelola operasional dan master data.
2. **Sopir** — menggunakan PWA mobile untuk menjalankan perjalanan dan mengirim lokasi GPS.
3. **User** — masyarakat/pengguna layanan yang dapat mencari trayek dan melihat posisi angkutan secara real-time.

Fokus MVP adalah menghasilkan alur end-to-end:

> Admin mengatur trayek → Sopir memilih kendaraan/trayek → Sopir memulai perjalanan → GPS dikirim → server mem-broadcast lokasi → User/Admin melihat angkutan bergerak pada peta secara real-time.

---

# 2. Tujuan Produk

## 2.1 Tujuan Utama

- Digitalisasi data trayek angkutan desa.
- Menyediakan informasi trayek dan tarif kepada masyarakat.
- Memungkinkan pemantauan posisi angkutan secara real-time.
- Membantu Dinas Perhubungan memonitor operasional kendaraan.
- Menyimpan histori perjalanan dan lokasi kendaraan.
- Menjadi dasar pengembangan sistem transportasi publik digital.

## 2.2 Tujuan Teknis

- Menggunakan Laravel 13 sebagai backend.
- Menyediakan REST API untuk PWA.
- Menggunakan Leaflet sebagai library peta.
- Menggunakan OpenStreetMap sebagai sumber map tiles.
- Menggunakan OSRM atau layanan routing setara untuk kebutuhan routing.
- Menggunakan Laravel Reverb/WebSocket untuk real-time tracking.
- Menggunakan Redis untuk cache, queue, dan kebutuhan real-time.
- Menggunakan MySQL/MariaDB sebagai database utama.
- Mendukung instalasi PWA pada perangkat mobile.

---

# 3. Scope Produk

## 3.1 In Scope — MVP

### Super Admin

- Login/logout.
- Dashboard.
- Manajemen user.
- Manajemen sopir.
- Manajemen kendaraan/angkutan.
- Manajemen trayek.
- Manajemen titik jalur/trayek.
- Manajemen titik pemberhentian.
- Manajemen tarif.
- Manajemen jadwal.
- Monitoring kendaraan aktif.
- Monitoring perjalanan aktif.
- Melihat histori perjalanan.

### Sopir

- Login.
- Dashboard mobile.
- Melihat kendaraan yang ditugaskan.
- Memilih trayek.
- Memulai perjalanan.
- Mengaktifkan GPS.
- Mengirim koordinat secara berkala.
- Melihat status GPS.
- Mengakhiri perjalanan.
- Melihat histori perjalanan.

### User

- Membuka aplikasi tanpa login.
- Melihat daftar trayek.
- Melihat detail trayek.
- Melihat tarif.
- Melihat titik pemberhentian.
- Melihat angkutan aktif.
- Melihat posisi angkutan secara real-time.
- Melihat posisi pengguna pada peta.
- Mencari trayek.
- Login opsional untuk fitur lanjutan.
- Favorit trayek/angkutan sebagai fitur lanjutan MVP.

---

# 4. Non-Goals MVP

Fitur berikut tidak menjadi prioritas tahap pertama:

- Pembayaran online.
- E-ticketing.
- Rating sopir.
- Chat antara user dan sopir.
- Prediksi ETA berbasis machine learning.
- Navigasi turn-by-turn untuk sopir.
- Integrasi sistem tilang.
- Integrasi pembayaran pemerintah.
- Integrasi CCTV.
- Native Android/iOS khusus sopir.

Fitur tersebut dapat masuk roadmap setelah core tracking stabil.

---

# 5. Role dan Hak Akses

## 5.1 Super Admin

Hak akses penuh terhadap sistem.

| Modul | View | Create | Update | Delete |
|---|---:|---:|---:|---:|
| User | ✓ | ✓ | ✓ | ✓ |
| Sopir | ✓ | ✓ | ✓ | ✓ |
| Kendaraan | ✓ | ✓ | ✓ | ✓ |
| Trayek | ✓ | ✓ | ✓ | ✓ |
| Route Points | ✓ | ✓ | ✓ | ✓ |
| Halte/Stops | ✓ | ✓ | ✓ | ✓ |
| Tarif | ✓ | ✓ | ✓ | ✓ |
| Jadwal | ✓ | ✓ | ✓ | ✓ |
| Trip | ✓ | - | - | - |
| Monitoring GPS | ✓ | - | - | - |
| Laporan | ✓ | - | - | - |

## 5.2 Sopir

- Melihat profil.
- Melihat kendaraan yang ditugaskan.
- Melihat trayek yang tersedia.
- Memulai perjalanan.
- Mengirim lokasi GPS.
- Mengakhiri perjalanan.
- Melihat histori perjalanan sendiri.

Sopir tidak dapat mengubah master data.

## 5.3 User

- Melihat informasi publik.
- Melihat trayek.
- Melihat tarif.
- Melihat kendaraan aktif.
- Melihat lokasi kendaraan.
- Mencari trayek.
- Mengelola favorit jika login.

---

# 6. User Flow

## 6.1 Flow Super Admin

```text
Login
  ↓
Dashboard
  ↓
Kelola Master Data
  ├── User
  ├── Sopir
  ├── Kendaraan
  ├── Trayek
  ├── Route Points
  ├── Stops
  ├── Tarif
  └── Jadwal
  ↓
Monitoring
  ↓
Lihat kendaraan aktif
  ↓
Lihat histori perjalanan
```

## 6.2 Flow Sopir

```text
Login
  ↓
Dashboard
  ↓
Pilih kendaraan
  ↓
Pilih trayek
  ↓
Mulai perjalanan
  ↓
Request permission GPS
  ↓
GPS aktif
  ↓
watchPosition()
  ↓
Kirim lokasi ke API
  ↓
Server validasi
  ↓
Simpan lokasi
  ↓
Broadcast WebSocket
  ↓
User/Admin menerima update
  ↓
Sopir selesai
  ↓
Stop GPS
  ↓
Trip completed
```

## 6.3 Flow User

```text
Buka PWA
  ↓
Home
  ↓
Cari/pilih trayek
  ↓
Detail trayek
  ↓
Lihat jalur pada peta
  ↓
Lihat kendaraan aktif
  ↓
Pilih kendaraan
  ↓
Live tracking
```

---

# 7. Arsitektur Sistem

```text
                         ┌──────────────────────┐
                         │      SUPER ADMIN     │
                         │       PWA/Web        │
                         └──────────┬───────────┘
                                    │
                         ┌──────────▼───────────┐
                         │      LARAVEL 13      │
                         │  Web + REST API      │
                         └───────┬─────┬────────┘
                                 │     │
                  ┌──────────────┘     └──────────────┐
                  ▼                                   ▼
             MySQL/MariaDB                         Redis
                  │                                   │
                  │                              Queue/Cache
                  │                                   │
                  └──────────────┬────────────────────┘
                                 ▼
                         Laravel Reverb
                              WebSocket
                                 │
                 ┌───────────────┴────────────────┐
                 ▼                                ▼
          ┌──────────────┐                ┌──────────────┐
          │  PWA SOPIR   │                │   PWA USER   │
          │              │                │              │
          │ GPS Device   │                │ Live Map     │
          └──────┬───────┘                └──────────────┘
                 │
                 ▼
        Browser Geolocation API

Map:
Leaflet + OpenStreetMap
Routing:
OSRM / Routing Provider
```

---

# 8. Teknologi

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13 |
| Language | PHP |
| Database | MySQL/MariaDB |
| Authentication | Laravel Sanctum |
| Authorization | Spatie Laravel Permission |
| Admin Panel | Filament |
| Frontend | Blade + Livewire + JavaScript |
| PWA | Web App Manifest + Service Worker |
| Map | Leaflet.js |
| Map Tiles | OpenStreetMap |
| Routing | OSRM |
| Real-time | Laravel Reverb |
| WebSocket Client | Laravel Echo |
| Cache | Redis |
| Queue | Redis |
| Web Server | Nginx |
| Process Manager | Supervisor/systemd |
| SSL | Let's Encrypt |

---

# 9. Modul Sistem

## 9.1 Authentication

Endpoint utama:

```http
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/me
```

Kebutuhan:

- Sanctum token/session.
- Password hashing.
- Rate limiting login.
- Validasi input.
- Role authorization.

---

# 10. Master User

Table:

```text
users
```

Field utama:

- id
- name
- email
- password
- role
- phone
- status
- timestamps

Role:

```text
super_admin
driver
user
```

Catatan: Jika menggunakan Spatie Permission, role sebaiknya dikelola melalui tabel roles/permissions dan field `role` pada users dapat dihindari agar tidak terjadi duplikasi sumber kebenaran.

---

# 11. Master Sopir

Table:

```text
drivers
```

Data:

- user.
- kode sopir.
- NIK.
- nomor telepon.
- nomor SIM.
- jenis SIM.
- alamat.
- foto.
- status.

Relasi:

```text
users 1 ───── 1 drivers
```

---

# 12. Master Kendaraan

Table:

```text
vehicles
```

Data:

- kode kendaraan.
- nomor polisi.
- jenis kendaraan.
- merek.
- model.
- warna.
- tahun.
- kapasitas.
- nama pemilik.
- foto.
- status.

Contoh:

```text
K001
AD 1234 XX
Angkutan Desa
Suzuki Carry
Putih
12 penumpang
```

---

# 13. Penugasan Sopir dan Kendaraan

Table:

```text
driver_vehicle_assignments
```

Tujuan:

- Menyimpan kendaraan yang digunakan sopir.
- Mendukung pergantian sopir.
- Menyimpan histori penugasan.

Field:

```text
id
driver_id
vehicle_id
started_at
ended_at
status
created_at
updated_at
```

Relasi:

```text
drivers 1 ───── * driver_vehicle_assignments * ───── 1 vehicles
```

Constraint yang direkomendasikan:

- Tidak boleh ada dua assignment aktif untuk kombinasi kendaraan yang sama.
- Tidak boleh ada dua kendaraan aktif yang ditugaskan sebagai kendaraan utama ke sopir pada waktu yang sama jika aturan bisnis menetapkan satu kendaraan per sopir.

---

# 14. Master Trayek

Table:

```text
routes
```

Field:

```text
id
route_code
name
description
origin
destination
distance_km
estimated_duration_minutes
status
created_at
updated_at
```

Contoh:

```text
TR-001
Pati - Tayu
Pati → Tayu
32.50 KM
75 menit
```

---

# 15. Route Points

Table:

```text
route_points
```

Tujuan:

Menyimpan koordinat pembentuk garis trayek.

Field:

```text
id
route_id
sequence
latitude
longitude
created_at
updated_at
```

Relasi:

```text
routes 1 ───── * route_points
```

Peta akan menggunakan data ini untuk membuat Leaflet Polyline.

Contoh:

```text
TR-001

1  -6.751234  111.034210
2  -6.752341  111.036420
3  -6.754321  111.039310
4  -6.756123  111.041210
```

---

# 16. Titik Pemberhentian

Table:

```text
route_stops
```

Field:

```text
id
route_id
name
sequence
latitude
longitude
description
status
created_at
updated_at
```

Relasi:

```text
routes 1 ───── * route_stops
```

Contoh:

```text
1. Terminal Pati
2. Margorejo
3. Wedarijaksa
4. Tayu
```

---

# 17. Jadwal Trayek

Table:

```text
route_schedules
```

Field:

```text
id
route_id
day_of_week
start_time
end_time
frequency_minutes
status
created_at
updated_at
```

Contoh:

```text
Senin-Jumat
06:00 - 18:00
Interval 20 menit
```

Untuk hari operasional yang lebih kompleks, dapat ditambahkan tabel `schedule_days`.

---

# 18. Tarif

## Model sederhana

Table:

```text
fares
```

Field:

```text
id
route_id
name
passenger_type
price
effective_date
status
created_at
updated_at
```

Contoh:

```text
Umum       Rp 5.000
Pelajar    Rp 3.000
Mahasiswa  Rp 4.000
```

## Model tarif berdasarkan titik

Jika tarif berbeda berdasarkan asal/tujuan:

```text
fare_rules
```

Field:

```text
id
route_id
from_stop_id
to_stop_id
price
effective_date
status
created_at
updated_at
```

Rekomendasi: mulai dari `fares`, dan gunakan `fare_rules` jika kebutuhan tarif berbasis zona/titik memang diperlukan.

---

# 19. Perjalanan / Trip

Table:

```text
trips
```

Field:

```text
id
route_id
driver_id
vehicle_id
started_at
ended_at
start_latitude
start_longitude
end_latitude
end_longitude
status
created_at
updated_at
```

Status:

```text
scheduled
active
completed
cancelled
```

Relasi:

```text
routes   1 ───── *
drivers  1 ───── *
vehicles 1 ───── *
trips
```

Business rule:

- Satu sopir tidak boleh mempunyai lebih dari satu trip aktif.
- Satu kendaraan tidak boleh mempunyai lebih dari satu trip aktif.
- Trip hanya dapat dimulai oleh sopir yang authenticated.
- Sopir hanya boleh menggunakan kendaraan yang ditugaskan kepadanya.

---

# 20. Vehicle Location

Table:

```text
vehicle_locations
```

Field:

```text
id
trip_id
vehicle_id
latitude
longitude
speed
heading
accuracy
recorded_at
created_at
```

Keterangan:

| Field | Keterangan |
|---|---|
| latitude | Latitude GPS |
| longitude | Longitude GPS |
| speed | Kecepatan |
| heading | Arah kendaraan |
| accuracy | Akurasi GPS |
| recorded_at | Waktu GPS direkam |

Frekuensi pengiriman yang direkomendasikan:

```text
10–15 detik
```

atau berdasarkan perubahan jarak:

```text
10–20 meter
```

Jangan mengirim GPS setiap 1 detik secara default karena akan meningkatkan penggunaan baterai, bandwidth, dan beban server.

---

# 21. Arsitektur GPS Real-Time

```text
HP SOPIR
   │
   │ Browser Geolocation API
   ▼
watchPosition()
   │
   ▼
POST /api/driver/trips/{trip}/location
   │
   ▼
Laravel API
   │
   ├── Authenticate
   ├── Validate Trip
   ├── Validate Driver
   ├── Validate Vehicle
   ├── Validate Coordinate
   │
   ▼
VehicleLocation
   │
   ▼
Redis / Event
   │
   ▼
Laravel Reverb
   │
   ▼
WebSocket
   │
   ├─────────────► Admin Monitoring
   │
   └─────────────► User Live Map
```

Event:

```text
VehicleLocationUpdated
```

Channel dapat menggunakan:

```text
routes.{routeId}
vehicles.{vehicleId}
trips.{tripId}
```

Rekomendasi:

- Channel publik untuk informasi kendaraan aktif yang memang boleh diketahui masyarakat.
- Channel private untuk data operasional/admin.
- Jangan expose informasi sensitif sopir.

---

# 22. API GPS

Endpoint:

```http
POST /api/driver/trips/{trip}/location
```

Request:

```json
{
    "latitude": -6.751234,
    "longitude": 111.034210,
    "speed": 32.5,
    "heading": 120,
    "accuracy": 8.2,
    "recorded_at": "2026-08-30T18:30:00+07:00"
}
```

Response:

```json
{
    "success": true,
    "message": "Location updated"
}
```

Validasi:

- latitude antara -90 sampai 90.
- longitude antara -180 sampai 180.
- trip harus aktif.
- driver harus memiliki hak terhadap trip.
- vehicle harus terkait dengan trip.
- timestamp tidak boleh terlalu jauh dari waktu server.
- koordinat abnormal dapat ditolak/ditandai.

---

# 23. Real-Time Map

## Super Admin

Menampilkan:

- Semua kendaraan aktif.
- Trayek.
- Route polyline.
- Stops.
- Posisi kendaraan.
- Kecepatan.
- Status trip.
- Waktu update terakhir.

## User

Menampilkan:

- Trayek yang dipilih.
- Polyline.
- Stops.
- Kendaraan aktif pada trayek.
- Marker kendaraan.
- Posisi user jika permission diberikan.

---

# 24. Leaflet

Leaflet bertugas sebagai map UI.

Fungsi:

- Menampilkan map.
- Marker kendaraan.
- Marker stop.
- Polyline trayek.
- Popup kendaraan.
- Center map.
- Update posisi marker.
- Menampilkan posisi user.

Leaflet bukan penyedia data peta.

Stack:

```text
Leaflet
+
OpenStreetMap
+
OSRM
```

OpenStreetMap digunakan sebagai map tiles dan OSRM/routing provider digunakan jika sistem membutuhkan perhitungan rute jalan.

Catatan produksi: penggunaan tile server publik OpenStreetMap harus mengikuti kebijakan penggunaan tile provider. Jika trafik aplikasi sudah besar, gunakan provider tile/routing khusus atau infrastruktur sendiri.

---

# 25. PWA

## PWA User

Fokus:

- Mobile-first.
- Installable.
- Responsive.
- Cache asset dasar.
- Bisa dibuka seperti aplikasi.

## PWA Sopir

Fokus:

- GPS.
- Start/stop trip.
- Status koneksi.
- Status GPS.
- Pengiriman lokasi.

Komponen:

```text
manifest.json
service-worker.js
icons
offline cache
```

Catatan penting:

PWA/browser memiliki keterbatasan background geolocation, terutama ketika layar terkunci atau browser dihentikan oleh sistem operasi. Untuk kebutuhan tracking operasional yang wajib berjalan stabil dalam kondisi background, aplikasi native/hybrid khusus sopir dapat dipertimbangkan pada fase berikutnya.

---

# 26. Struktur Database

Diagram konseptual:

```text
users
  │
  └────────────── drivers
                       │
                       │
                       ▼
              driver_vehicle_assignments
                       │
                       ▼
                    vehicles
                       │
                       │
                       ▼
                     trips
                       │
             ┌─────────┴─────────┐
             ▼                   ▼
          routes         vehicle_locations
             │
      ┌──────┼───────────────┐
      ▼      ▼               ▼
route_points route_stops route_schedules

routes
  │
  └──── fares
```

---

# 27. ERD Detail

```text
USERS
-----
id PK
name
email
password
phone
status
timestamps

        1
        │
        │
        1
DRIVERS
-------
id PK
user_id FK
driver_code
nik
phone
license_number
license_type
address
photo
status
timestamps

        1
        │
        │
        *
DRIVER_VEHICLE_ASSIGNMENTS
--------------------------
id PK
driver_id FK
vehicle_id FK
started_at
ended_at
status
timestamps

        *
        │
        │
        1
VEHICLES
--------
id PK
vehicle_code
plate_number
vehicle_type
brand
model
color
year
capacity
owner_name
photo
status
timestamps


ROUTES
------
id PK
route_code
name
description
origin
destination
distance_km
estimated_duration_minutes
status
timestamps

   │
   ├─────────────── *
   │             ROUTE_POINTS
   │             ------------
   │             id PK
   │             route_id FK
   │             sequence
   │             latitude
   │             longitude
   │
   ├─────────────── *
   │             ROUTE_STOPS
   │             -----------
   │             id PK
   │             route_id FK
   │             name
   │             sequence
   │             latitude
   │             longitude
   │             description
   │             status
   │
   ├─────────────── *
   │             ROUTE_SCHEDULES
   │             ---------------
   │             id PK
   │             route_id FK
   │             day_of_week
   │             start_time
   │             end_time
   │             frequency_minutes
   │             status
   │
   └─────────────── *
                 FARES
                 -----
                 id PK
                 route_id FK
                 name
                 passenger_type
                 price
                 effective_date
                 status


TRIPS
-----
id PK
route_id FK
driver_id FK
vehicle_id FK
started_at
ended_at
start_latitude
start_longitude
end_latitude
end_longitude
status
timestamps

   │
   └─────────────── *
                 VEHICLE_LOCATIONS
                 -----------------
                 id PK
                 trip_id FK
                 vehicle_id FK
                 latitude
                 longitude
                 speed
                 heading
                 accuracy
                 recorded_at
                 created_at
```

---

# 28. Index Database

Index penting:

```text
users.email UNIQUE

drivers.user_id UNIQUE
drivers.driver_code UNIQUE
drivers.nik INDEX

vehicles.vehicle_code UNIQUE
vehicles.plate_number UNIQUE

routes.route_code UNIQUE

route_points(route_id, sequence)
route_stops(route_id, sequence)

trips(route_id, status)
trips(driver_id, status)
trips(vehicle_id, status)

vehicle_locations(trip_id, recorded_at)
vehicle_locations(vehicle_id, recorded_at)
vehicle_locations(recorded_at)
```

Untuk tabel `vehicle_locations`, data dapat berkembang sangat cepat. Jangan membuat query histori tanpa index.

---

# 29. Retensi Data GPS

GPS dapat menghasilkan data besar.

Contoh:

```text
1 kendaraan
1 titik / 10 detik
= 360 titik / jam
= 2.880 titik / 8 jam
= 86.400 titik / 30 hari
```

Jika terdapat 100 kendaraan:

```text
8.640.000 titik / 30 hari
```

Strategi:

### Data real-time

Simpan update terbaru di cache/Redis jika diperlukan.

### Data histori

Simpan pada database.

### Data lama

Pertimbangkan:

- archive.
- agregasi.
- partisi tabel.
- retention policy.

Contoh kebijakan:

```text
0–3 bulan     : detail GPS
3–12 bulan    : data teragregasi
>12 bulan     : archive/delete sesuai kebijakan Dishub
```

Kebijakan final harus disesuaikan dengan kebutuhan instansi.

---

# 30. Dashboard Super Admin

Dashboard minimal:

```text
┌─────────────────────────────────────────┐
│ TOTAL ANGKUTAN       120                │
│ SOPIR                 135               │
│ TRAYEK                 15               │
│ AKTIF                  42               │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│          LIVE MONITORING MAP             │
│                                         │
│      🚌                                 │
│          ●────────────●                 │
│                     🚌                  │
│                                         │
│  🚌                                    │
└─────────────────────────────────────────┘

Trip Aktif
-------------------------------------------
Kendaraan | Sopir | Trayek | Speed | Update
AD 1234   | Budi  | Pati-Tayu | 32 | 5s
```

---

# 31. Dashboard Sopir

```text
┌──────────────────────────┐
│ ANGKUTAN DESA            │
│                          │
│ Budi                     │
│ AD 1234 XX               │
│                          │
│ Trayek                   │
│ Pati → Tayu              │
│                          │
│ GPS: ● ACTIVE             │
│                          │
│ [ MULAI PERJALANAN ]     │
└──────────────────────────┘
```

Ketika aktif:

```text
GPS ● ACTIVE

Speed      32 km/h
Distance   12.4 km
Duration   00:45:32

[ SELESAIKAN PERJALANAN ]
```

---

# 32. Dashboard User

```text
┌──────────────────────────┐
│ 🚐 ANGKUTAN DESA         │
│                          │
│ 🔍 Cari trayek...        │
│                          │
│ Pati → Tayu              │
│ 5 angkutan aktif         │
│                          │
│ Pati → Juwana             │
│ 3 angkutan aktif         │
└──────────────────────────┘
```

---

# 33. Live Tracking User

```text
Pati → Tayu

       🚌
        │
    ●───●────●
   /          \
  ●            ●
                \
                 ●

AD 1234 XX
Speed: 32 km/h
Last update: 5 detik lalu
```

---

# 34. API Blueprint

## Public

```http
GET /api/routes
GET /api/routes/{route}
GET /api/routes/{route}/stops
GET /api/routes/{route}/vehicles
GET /api/vehicles
GET /api/vehicles/{vehicle}
GET /api/vehicles/active
GET /api/vehicles/{vehicle}/location
```

## Driver

```http
GET  /api/driver/dashboard
GET  /api/driver/vehicles
GET  /api/driver/routes
GET  /api/driver/trips/current
GET  /api/driver/trips/history

POST /api/driver/trips/start
POST /api/driver/trips/{trip}/location
POST /api/driver/trips/{trip}/stop
```

## Admin

Admin CRUD dapat menggunakan route/controller khusus:

```http
GET    /api/admin/routes
POST   /api/admin/routes
GET    /api/admin/routes/{route}
PUT    /api/admin/routes/{route}
DELETE /api/admin/routes/{route}
```

Pola yang sama diterapkan untuk:

```text
users
drivers
vehicles
routes
route_points
route_stops
route_schedules
fares
```

---

# 35. Struktur Laravel

```text
app/
├── Console/
├── Events/
│   └── VehicleLocationUpdated.php
│
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   └── Api/
│   │       ├── Auth/
│   │       ├── Driver/
│   │       └── User/
│   │
│   ├── Requests/
│   │   ├── Auth/
│   │   ├── Driver/
│   │   └── Admin/
│   │
│   └── Resources/
│
├── Models/
│   ├── User.php
│   ├── Driver.php
│   ├── Vehicle.php
│   ├── DriverVehicleAssignment.php
│   ├── Route.php
│   ├── RoutePoint.php
│   ├── RouteStop.php
│   ├── RouteSchedule.php
│   ├── Fare.php
│   ├── Trip.php
│   └── VehicleLocation.php
│
├── Services/
│   ├── GpsTrackingService.php
│   ├── TripService.php
│   ├── RouteService.php
│   └── VehicleService.php
│
├── Policies/
├── Jobs/
└── Notifications/

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── views/
│   ├── admin/
│   ├── driver/
│   └── user/
│
├── js/
│   ├── app.js
│   ├── maps/
│   ├── gps/
│   └── realtime/
│
├── css/
└── pwa/

routes/
├── web.php
├── api.php
└── channels.php
```

---

# 36. Service Layer

Jangan menaruh seluruh logic bisnis di Controller.

Contoh:

```text
GpsTrackingService
```

Tanggung jawab:

- Validasi trip.
- Validasi driver.
- Validasi vehicle.
- Simpan lokasi.
- Menentukan update terakhir.
- Broadcast event.

Contoh:

```php
GpsTrackingService::updateLocation(
    Trip $trip,
    array $location
);
```

`TripService`:

```text
startTrip()
stopTrip()
cancelTrip()
getActiveTrip()
```

`RouteService`:

```text
createRoute()
updateRoute()
saveRoutePoints()
getRouteWithStops()
```

---

# 37. Event Real-Time

Event:

```text
VehicleLocationUpdated
```

Payload:

```json
{
    "vehicle_id": 12,
    "trip_id": 100,
    "route_id": 5,
    "latitude": -6.751234,
    "longitude": 111.034210,
    "speed": 32.5,
    "heading": 120,
    "recorded_at": "2026-08-30T18:30:00+07:00"
}
```

Frontend:

```text
WebSocket event
       ↓
Cari marker vehicle_id
       ↓
Update LatLng
       ↓
Animasi perpindahan marker
```

---

# 38. State Kendaraan

Kendaraan dapat memiliki status:

```text
active
inactive
maintenance
```

Sedangkan status operasional saat ini ditentukan dari trip:

```text
idle
on_trip
```

Jangan mencampur status master kendaraan dengan status perjalanan.

---

# 39. Status GPS

Sistem sebaiknya membedakan:

```text
GPS ACTIVE
GPS WEAK
GPS OFFLINE
GPS STALE
```

Contoh aturan:

```text
Last update <= 30 detik
→ ACTIVE

30–90 detik
→ DELAYED

> 90 detik
→ OFFLINE/STALE
```

Nilai dapat disesuaikan.

---

# 40. Keamanan

## Authentication

- Laravel Sanctum.
- Password menggunakan hashing Laravel.
- HTTPS wajib di production.
- Token/session harus dapat dicabut.

## Authorization

- Policy/Gate.
- Spatie Permission.
- Driver hanya dapat mengakses trip miliknya.
- User tidak boleh mengakses endpoint admin.

## GPS

Server tidak boleh percaya begitu saja pada koordinat client.

Validasi:

```text
authenticated?
      ↓
driver valid?
      ↓
trip active?
      ↓
driver assigned?
      ↓
vehicle valid?
      ↓
coordinate valid?
      ↓
timestamp valid?
      ↓
save + broadcast
```

## Rate Limit

Endpoint GPS perlu rate limiting agar tidak dapat disalahgunakan.

---

# 41. Standar Baku Pengembangan (Coding Standards)

Bagian ini bersifat **wajib** dan mengikat seluruh proses pengembangan sistem, bukan sekadar rekomendasi. Setiap kontribusi kode (developer internal maupun AI coding assistant) harus mematuhi aturan berikut.

## 41.1 Query Builder Wajib Ditaruh di Model

- Seluruh akses database (Query Builder maupun Eloquent) **wajib** ditulis di dalam **Model** (atau Repository/Service yang memanggil Model), **tidak boleh** ada query langsung (`DB::table()`, `DB::select()`, `Model::where()`, dsb.) yang ditulis di dalam Controller.
- Controller **hanya** boleh:
  - Menerima request (idealnya melalui Form Request untuk validasi).
  - Memanggil method Service/Model.
  - Mengembalikan response (view, JSON, redirect).
- Setiap Model wajib menyediakan method scope/query yang jelas namanya (self-explanatory), misalnya:

  ```php
  // app/Models/VehicleLocation.php
  class VehicleLocation extends Model
  {
      public function scopeActiveTrip(Builder $query, int $tripId): Builder
      {
          return $query->where('trip_id', $tripId)
                       ->where('is_latest', true);
      }

      public static function latestByVehicle(int $vehicleId): ?self
      {
          return static::query()
              ->select(['id', 'vehicle_id', 'trip_id', 'latitude', 'longitude', 'recorded_at'])
              ->where('vehicle_id', $vehicleId)
              ->latest('recorded_at')
              ->first();
      }
  }
  ```

- Jika logic query kompleks (join banyak tabel, subquery, agregasi laporan), tetap ditaruh sebagai method di Model, lalu **dipanggil dari Service**, dan Service dipanggil dari Controller. Alur wajib:

  ```text
  Controller → Service → Model (Query Builder/Eloquent) → Database
  ```

- Controller yang kedapatan memuat query mentah dianggap **tidak lolos code review** dan wajib direfactor sebelum merge.
- Query Builder lebih diutamakan dibanding raw SQL (`DB::raw`, `DB::statement`) kecuali untuk kasus yang benar-benar tidak dapat diwakili Query Builder/Eloquent (misalnya fungsi geospasial tertentu). Jika terpaksa memakai raw SQL, wajib menggunakan parameter binding, tidak boleh concatenation string dari input user.

## 41.2 Keamanan (Security Hardening)

Selain poin keamanan yang sudah dijelaskan di Bagian 40 dan 44, seluruh pengembangan wajib mengikuti standar berikut:

- **Mass assignment protection** — setiap Model wajib mendefinisikan `$fillable` (whitelist), tidak menggunakan `$guarded = []` secara sembarangan.
- **Validasi input** — setiap endpoint wajib menggunakan Form Request (`php artisan make:request`) dengan rules yang eksplisit; tidak melakukan validasi manual yang tersebar di Controller.
- **SQL Injection** — karena seluruh query menggunakan Query Builder/Eloquent (bind parameter otomatis), input user tidak boleh pernah disisipkan langsung ke string query.
- **Mass query authorization** — setiap query yang mengembalikan data milik user/sopir tertentu wajib difilter berdasarkan kepemilikan (mis. `->where('driver_id', auth()->id())`), tidak hanya mengandalkan filter di frontend.
- **XSS** — output ke Blade wajib menggunakan `{{ }}` (auto-escape), hindari `{!! !!}` kecuali untuk konten yang sudah disanitasi.
- **CSRF** — wajib aktif untuk seluruh form berbasis session (default Laravel), API menggunakan Sanctum token.
- **Rate limiting** — wajib diterapkan pada endpoint auth, endpoint pengiriman GPS, dan endpoint publik yang rawan disalahgunakan (`throttle` middleware).
- **Least privilege** — akun database production tidak boleh menggunakan user root; hanya diberi hak sesuai kebutuhan (SELECT/INSERT/UPDATE/DELETE pada database terkait saja).
- **Secrets management** — kredensial (DB, Redis, API key routing/OSRM) wajib disimpan di `.env`, tidak boleh di-hardcode atau di-commit ke repository.
- **Audit trail** — perubahan pada master data penting (trayek, tarif, kendaraan, penugasan sopir) sebaiknya dicatat (created_by/updated_by atau activity log) untuk kebutuhan audit.
- **Dependency security** — jalankan `composer audit` / update berkala terhadap package yang memiliki celah keamanan.

## 41.3 Optimasi Query (Performance)

Karena sistem ini menangani data GPS bervolume tinggi dan diakses real-time, setiap query wajib dioptimasi agar sistem tidak lambat:

- **Hindari N+1 query** — gunakan eager loading (`with()`, `load()`) setiap kali mengambil data relasi (contoh: `Trip::with(['driver', 'vehicle', 'route'])`), bukan lazy loading di dalam loop.
- **Select kolom yang diperlukan saja** — hindari `select(*)` implisit untuk tabel besar (khususnya `vehicle_locations`); gunakan `->select([...])` agar payload dan I/O database lebih ringan.
- **Indexing wajib** — setiap kolom yang sering dipakai untuk `WHERE`, `JOIN`, dan `ORDER BY` (mis. `trip_id`, `vehicle_id`, `driver_id`, `recorded_at`, `route_id`) wajib memiliki index, mengacu pada Bagian 28 (Index Database).
- **Pagination wajib** — setiap listing data (histori trip, laporan, daftar kendaraan/sopir) wajib menggunakan `paginate()` atau cursor pagination, tidak boleh menarik seluruh data sekaligus.
- **Chunking untuk data besar** — proses batch pada tabel `vehicle_locations` (mis. agregasi laporan, pembersihan data lama sesuai retensi di Bagian 29) wajib menggunakan `chunk()`/`chunkById()` atau `lazy()`, bukan `get()` biasa.
- **Cache untuk data yang jarang berubah** — data master yang jarang berubah (trayek, tarif, jadwal, daftar stop) sebaiknya di-cache dengan Redis dan diinvalidasi saat ada perubahan (cache tagging/versioning).
- **Query real-time GPS harus ringan** — endpoint yang dipanggil frekuensi tinggi (update lokasi, polling status) wajib menghindari join berat; gunakan tabel/kolom ringkas (mis. `is_latest` flag) daripada `ORDER BY` + `LIMIT` pada tabel besar setiap request.
- **Gunakan `exists()`/`count()` yang tepat** — jangan mengambil seluruh baris hanya untuk mengecek keberadaan data; gunakan `exists()`, bukan `count() > 0` atau `get()->isNotEmpty()`.
- **Database transaction** — operasi yang melibatkan lebih dari satu tabel (mis. mulai trip + update status kendaraan) wajib dibungkus `DB::transaction()` agar konsisten.
- **Query logging saat development** — gunakan Laravel Debugbar/Telescope selama development untuk memantau jumlah query per request dan mendeteksi query lambat sebelum masuk production.

## 41.4 Standar Umum Pengembangan Web yang Proper

- Mengikuti **PSR-12** untuk coding style PHP, gunakan Laravel Pint untuk formatting otomatis.
- Struktur mengikuti pola **Controller (tipis) → Service (business logic) → Model (query builder/Eloquent)**, konsisten dengan Bagian 35 dan 36.
- Setiap fitur baru disertai **migration** (bukan mengubah struktur tabel manual di production).
- Penamaan tabel, kolom, route, dan variabel konsisten (snake_case untuk database, camelCase untuk PHP variable/method sesuai konvensi Laravel).
- Response API konsisten menggunakan API Resource (`JsonResource`), tidak mengembalikan Model mentah.
- Setiap endpoint penting (terutama trip, GPS, dan data finansial/tarif) wajib memiliki automated test (unit/feature test) sebelum dianggap selesai.
- Error dan exception ditangani secara terpusat (Laravel exception handler), tidak menampilkan stack trace/detail teknis ke end user di production.
- Logging aktivitas penting (login gagal, GPS ditolak, trip gagal disimpan) menggunakan Laravel Log channel yang sesuai, bukan `dd()`/`var_dump()` yang tertinggal di kode.
- Code review wajib dilakukan sebelum merge ke branch utama, dengan checklist minimal: tidak ada query di Controller, tidak ada credential hardcoded, validasi input lengkap, query sudah dioptimasi (eager loading/index/pagination).

## 41.5 Aturan Wajib View CRUD Berdasarkan Modul User

- Seluruh tampilan CRUD di aplikasi wajib mengikuti pola view yang sudah ada pada modul **User** sebagai acuan utama, baik dari sisi struktur halaman, tata letak, komponen, maupun interaksi pengguna.
- Setiap modul baru (misalnya Driver, Vehicle, Route, Route Stop, Fare, Schedule, Trip) harus memiliki view yang konsisten dengan pola berikut:
  - halaman list/index dengan search/filter dan pagination,
  - tombol aksi create, edit, detail, delete,
  - halaman create/edit dengan form yang rapi dan validasi yang jelas,
  - penggunaan flash message untuk status sukses/error,
  - tabel/daftar yang konsisten lintas modul,
  - penggunaan layout, spacing, warna, dan state UI yang seragam.
- Modul User dipakai sebagai **template visual default** untuk CRUD. Jika ada perubahan desain, perubahan tersebut harus tetap mempertahankan konsistensi struktur dan pola interaksi yang sama.
- Controller, Service, dan View harus dibangun sedemikian rupa agar setiap CRUD memiliki pengalaman pengguna yang seragam, tidak acak atau custom per modul tanpa alasan bisnis yang kuat.
- Desain yang berbeda hanya boleh dibuat jika ada kebutuhan fungsional spesifik, tetap harus mengikuti pola dasar modul User dan tidak mengurangi konsistensi antarmuka.

---

# 42. Offline dan Koneksi Buruk

Karena sopir dapat berada di area dengan sinyal lemah, PWA harus memiliki strategi:

```text
GPS mendapatkan lokasi
        ↓
Internet tersedia?
   ┌────┴────┐
  YES       NO
   │         │
   ▼         ▼
Kirim API   Simpan lokal
             │
             ▼
        Internet kembali
             │
             ▼
       Sinkronisasi
```

Untuk MVP, dapat menggunakan IndexedDB untuk antrean lokasi yang gagal dikirim.

Perlu diperhatikan bahwa data GPS yang disimpan lokal harus dibatasi agar tidak memenuhi storage perangkat.

---

# 43. Error Handling

Contoh:

### GPS permission ditolak

Tampilkan:

```text
GPS tidak diizinkan.
Aktifkan izin lokasi untuk memulai perjalanan.
```

### GPS tidak tersedia

```text
Lokasi GPS belum tersedia.
Pastikan Location/GPS perangkat aktif.
```

### Internet terputus

```text
Koneksi terputus.
Lokasi akan disinkronkan ketika koneksi kembali.
```

### Trip sudah selesai

API:

```text
409 Conflict
```

### Driver tidak memiliki akses

```text
403 Forbidden
```

---

# 44. Non-Functional Requirements

## Performance

Target MVP:

- Dashboard admin < 3 detik pada kondisi jaringan normal.
- API response umum < 500 ms jika tidak melakukan operasi berat.
- Update lokasi real-time diterima client dalam beberapa detik setelah server menerima data.

## Availability

Production harus menggunakan:

- HTTPS.
- Process manager.
- Queue worker.
- Redis.
- Database backup.
- Monitoring.

## Security

- HTTPS.
- CSRF untuk web session.
- Sanctum untuk API.
- Validation.
- Authorization.
- Rate limiting.
- Secure headers.
- Backup database.

## Scalability

Arsitektur harus memungkinkan penambahan:

- jumlah kendaraan.
- jumlah trayek.
- jumlah user.
- jumlah koneksi WebSocket.

---

# 45. Monitoring Server

Production:

```text
Nginx
   │
   ├── Laravel PHP-FPM
   │
   ├── Reverb
   │
   ├── Queue Worker
   │
   └── Scheduler
        │
        ▼
      Redis
        │
        ▼
      MySQL
```

Monitoring:

- CPU.
- RAM.
- Disk.
- Redis.
- MySQL.
- Queue.
- Reverb.
- Error log.
- GPS update rate.

---

# 46. Backup

Minimum:

```text
Database backup harian
Database backup mingguan
Backup disimpan di lokasi berbeda
```

Untuk tabel GPS yang besar, backup dapat menggunakan strategi khusus dan tidak harus selalu diperlakukan sama seperti tabel master.

---

# 47. Acceptance Criteria MVP

## Admin

- [ ] Admin dapat login.
- [ ] Admin dapat membuat sopir.
- [ ] Admin dapat membuat kendaraan.
- [ ] Admin dapat membuat trayek.
- [ ] Admin dapat menambahkan route points.
- [ ] Admin dapat menambahkan stops.
- [ ] Admin dapat mengatur tarif.
- [ ] Admin dapat mengatur jadwal.
- [ ] Admin dapat melihat kendaraan aktif.
- [ ] Admin dapat melihat histori trip.

## Sopir

- [ ] Sopir dapat login.
- [ ] Sopir dapat melihat kendaraan.
- [ ] Sopir dapat memilih trayek.
- [ ] Sopir dapat memulai trip.
- [ ] Browser meminta izin GPS.
- [ ] GPS berhasil dikirim ke server.
- [ ] Sopir dapat melihat status GPS.
- [ ] Sopir dapat mengakhiri trip.
- [ ] Histori trip tersimpan.

## User

- [ ] User dapat membuka aplikasi.
- [ ] User dapat melihat trayek.
- [ ] User dapat melihat detail trayek.
- [ ] User dapat melihat tarif.
- [ ] User dapat melihat stops.
- [ ] User dapat melihat kendaraan aktif.
- [ ] User dapat melihat posisi kendaraan.
- [ ] Marker kendaraan diperbarui secara real-time.

---

# 48. Roadmap Pengembangan

## Phase 1 — Project Foundation

```text
Laravel 13
Authentication
Roles
Permission
Base Layout
Database
Seeder
```

## Phase 2 — Master Data

```text
User
Driver
Vehicle
Assignment
Route
Route Point
Route Stop
Fare
Schedule
```

## Phase 3 — Trip Management

```text
Start Trip
Active Trip
Stop Trip
Trip History
```

## Phase 4 — GPS

```text
Browser Geolocation
GPS API
Vehicle Locations
GPS validation
```

## Phase 5 — Real-Time

```text
Redis
Laravel Reverb
Laravel Echo
VehicleLocationUpdated
Live Marker
```

## Phase 6 — PWA

```text
Manifest
Service Worker
Installable
Mobile UI
Offline basic support
IndexedDB GPS queue
```

## Phase 7 — Monitoring

```text
Admin Live Map
Vehicle status
Trip monitoring
GPS history
```

## Phase 8 — Reporting

```text
Trip report
Vehicle report
Driver report
Route report
Export Excel
Export PDF
```

---

# 49. Phase 2 / Future Features

Setelah MVP stabil:

## Nearby Vehicle

```text
Lokasi User
     ↓
Cari kendaraan radius tertentu
     ↓
Urutkan berdasarkan jarak
```

## ETA

```text
Vehicle GPS
     +
Road Route
     +
Speed
     ↓
Estimated Arrival
```

## Geofencing

Mendeteksi kendaraan keluar dari trayek.

```text
Route corridor
      │
      ▼
Vehicle position
      │
      ├── Inside → normal
      │
      └── Outside → alert
```

## Trip Replay

Admin dapat memutar ulang histori perjalanan.

```text
06:30 ───────────────── 07:45
       ●──●──●──●──●
              ▶
```

## Notifikasi

- Kendaraan mendekati stop.
- Kendaraan keluar trayek.
- GPS offline.
- Trip selesai.
- Kendaraan masuk maintenance.

---

# 50. Prinsip Pengembangan

1. **Master data harus menjadi sumber kebenaran utama.**
2. **Trip menjadi sumber status operasional kendaraan.**
3. **GPS location menjadi data telemetry, bukan master data.**
4. **Controller tipis, business logic berada di Service.**
5. **Semua endpoint penting memiliki authorization.**
6. **Real-time menggunakan event/WebSocket, bukan polling agresif.**
7. **Mobile-first untuk PWA sopir dan user.**
8. **Database GPS harus dirancang untuk volume besar.**
9. **Semua koordinat menggunakan decimal dengan precision yang memadai.**
10. **Gunakan UTC atau timezone yang konsisten di backend dan konversi tampilan ke Asia/Jakarta.**
11. **Jangan mengunci sistem pada satu provider map/routing jika belum diperlukan.**
12. **Mulai dari MVP yang sederhana, kemudian tambahkan fitur operasional.**

---

# 51. Definition of Done MVP

MVP dianggap selesai jika skenario berikut berhasil:

```text
1. Admin login
        ↓
2. Admin membuat sopir
        ↓
3. Admin membuat kendaraan
        ↓
4. Admin membuat trayek
        ↓
5. Admin menentukan route points + stops
        ↓
6. Admin membuat tarif
        ↓
7. Admin menugaskan kendaraan ke sopir
        ↓
8. Sopir login dari HP
        ↓
9. Sopir memilih kendaraan
        ↓
10. Sopir memilih trayek
        ↓
11. Sopir menekan "Mulai Perjalanan"
        ↓
12. GPS aktif
        ↓
13. Lokasi dikirim ke Laravel
        ↓
14. Laravel menyimpan lokasi
        ↓
15. Laravel Reverb broadcast event
        ↓
16. Admin menerima posisi kendaraan
        ↓
17. User menerima posisi kendaraan
        ↓
18. Marker kendaraan bergerak di Leaflet
        ↓
19. Sopir menekan "Selesai"
        ↓
20. Trip menjadi completed
        ↓
21. Histori perjalanan tersimpan
```

Jika seluruh alur tersebut berjalan stabil, core product sudah dapat dianggap berhasil.

---

# 52. Prioritas Implementasi

Gunakan prioritas berikut:

```text
P0 — WAJIB
├── Authentication
├── Role & Permission
├── Driver
├── Vehicle
├── Route
├── Route Points
├── Trip
├── GPS
├── Real-time
└── Live Map

P1 — PENTING
├── Stops
├── Fare
├── Schedule
├── Trip History
├── Admin Monitoring
└── PWA

P2 — PENGEMBANGAN
├── Nearby Vehicle
├── ETA
├── Geofencing
├── Notifications
├── Reports
└── Trip Replay

P3 — FUTURE
├── Native Driver App
├── Analytics
├── Predictive ETA
├── Public API
└── Integrasi sistem eksternal
```

---

# 53. Rekomendasi Urutan Coding

Jangan langsung membuat GPS.

Urutan implementasi:

```text
01. Laravel 13 setup
02. Database + migrations
03. Authentication
04. Role & Permission
05. User management
06. Driver management
07. Vehicle management
08. Driver-Vehicle Assignment
09. Route management
10. Route Point management
11. Route Stop management
12. Fare management
13. Schedule management
14. Trip management
15. Driver PWA
16. Browser GPS
17. GPS API
18. Vehicle Location
19. Redis
20. Laravel Reverb
21. Laravel Echo
22. User Live Map
23. Admin Live Map
24. PWA optimization
25. Offline handling
26. Testing
27. Production deployment
```

---

# 54. Kesimpulan Arsitektur

Core sistem:

```text
MASTER DATA
    │
    ├── Driver
    ├── Vehicle
    ├── Route
    ├── Stop
    ├── Fare
    └── Schedule
          │
          ▼
        TRIP
          │
          ▼
      GPS LOCATION
          │
          ▼
        REVERB
          │
          ▼
     REAL-TIME MAP
          │
     ┌────┴─────┐
     ▼          ▼
   ADMIN       USER
```

Teknologi inti:

```text
Laravel 13
    +
MySQL/MariaDB
    +
Redis
    +
Laravel Reverb
    +
Leaflet
    +
OpenStreetMap
    +
OSRM
    +
PWA
```

Prinsip utama proyek:

> **Admin mengelola data, sopir mengirim posisi, server memvalidasi dan mendistribusikan posisi, sedangkan masyarakat melihat informasi angkutan secara real-time.**
