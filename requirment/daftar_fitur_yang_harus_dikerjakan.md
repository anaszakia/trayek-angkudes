# Daftar Fitur Prioritas Awal Implementasi

Dokumen ini berisi urutan fitur yang harus dikerjakan terlebih dahulu agar sistem dapat berjalan sesuai MVP PRD. Fokus utama adalah membangun fondasi yang benar sebelum masuk ke real-time tracking, map, dan PWA.

---

## Prioritas P0 — Wajib Dikerjakan Dulu

### 1. Authentication & Authorization
- Login admin
- Login sopir
- Login user opsional
- Logout
- Role user
- Permission dasar
- Middleware akses per role

Tujuan:
- memastikan admin, sopir, dan user memiliki akses sesuai kebutuhan
- mencegah sopir dan user mengakses endpoint admin

---

### 2. Manajemen User
- CRUD user
- status user
- mapping ke role
- relasi user ke driver

Tujuan:
- admin dapat membuat dan mengelola akun sistem
- dasar untuk autentikasi dan manajemen sopir

---

### 3. Manajemen Sopir
- data profil sopir
- kode sopir
- NIK
- SIM
- alamat
- status aktif/nonaktif
- relasi ke user

Tujuan:
- menyimpan data driver secara konsisten
- siap digunakan dalam trip dan assignment

---

### 4. Manajemen Kendaraan
- kode kendaraan
- nomor polisi
- tipe kendaraan
- merek/model
- kapasitas
- status kendaraan
- foto jika dibutuhkan

Tujuan:
- admin memiliki data kendaraan yang valid
- kendaraan siap dipakai untuk perjalanan aktif

---

### 5. Penugasan Sopir - Kendaraan
- daftar assignment sopir ke kendaraan
- history assignment
- status aktif/nonaktif
- validasi satu kendaraan tidak dipakai dua sopir aktif

Tujuan:
- memastikan trip hanya bisa dijalankan oleh sopir yang berhak memakai kendaraan tertentu

---

### 6. Manajemen Trayek
- kode trayek
- nama trayek
- asal-tujuan
- jarak
- durasi estimasi
- status trayek

Tujuan:
- menyiapkan data route utama untuk operasional

---

### 7. Route Points
- koordinat titik pembentuk jalur
- sequence titik
- polyline trayek

Tujuan:
- membentuk garis trayek di peta
- dasar visualisasi rute di admin dan user

---

### 8. Route Stops
- nama halte atau stop
- urutan stop
- koordinat stop
- deskripsi

Tujuan:
- menampilkan pemberhentian trayek
- data penting untuk user dan monitoring

---

### 9. Tarif dan Jadwal
- tarif per route
- jenis penumpang
- jadwal operasional
- rentang waktu operasional

Tujuan:
- user dapat melihat informasi tarif dan jadwal
- admin dapat mengatur informasi yang dibutuhkan masyarakat

---

### 10. Trip Management
- start trip
- stop trip
- trip aktif
- histori trip
- validasi hanya satu trip aktif per sopir/vehicle

Tujuan:
- menjadi inti operasional sistem
- menentukan status kendaraan dan perjalanan

---

### 11. GPS API
- endpoint update lokasi
- validasi latitude/longitude
- validasi trip aktif
- validasi driver dan vehicle
- validasi timestamp
- rate limit

Tujuan:
- menerima lokasi dari perangkat sopir
- mencegah input GPS yang tidak valid

---

### 12. Vehicle Location
- simpan lokasi per titik GPS
- record timestamp
- speed, heading, accuracy
- penyimpanan data lokasi untuk histori

Tujuan:
- menyiapkan basis data telemetry untuk tracking
- dasar live map dan laporan

---

### 13. Real-Time Tracking
- event lokasi kendaraan
- broadcast via Laravel Reverb
- update lokasi real-time
- channel per vehicle / trip / route

Tujuan:
- admin dan user menerima perubahan posisi kendaraan secara real-time

---

### 14. Live Map Admin & User
- peta Leaflet
- marker kendaraan
- polyline trayek
- marker stop
- update posisi marker otomatis

Tujuan:
- memantau kendaraan aktif di map
- menampilkan informasi public kepada user

---

---

## Prioritas P1 — Penting Setelah Core Sistem Stabil

### 15. Dashboard Admin
- total kendaraan
- total sopir
- jumlah trayek
- kendaraan aktif
- trip aktif
- monitoring map

### 16. Dashboard Sopir
- status GPS
- kendaraan yang ditugaskan
- trayek aktif
- tombol mulai/selesai perjalanan
- informasi kecepatan dan durasi

### 17. Dashboard User
- daftar trayek
- status angkutan aktif
- detail trayek
- tarif
- titik stop
- live tracking

### 18. PWA Sopir dan User
- installable app
- responsive mobile
- cache dasar
- antarmuka mobile-first

### 19. Offline Handling
- simpans lokasi saat koneksi buruk
- sinkronisasi saat koneksi kembali
- batas ukuran data lokal

---

## Prioritas P2 — Fitur Pengembangan Lanjutan

- Nearby vehicle
- ETA
- Geofencing
- Notifikasi
- Trip replay
- Laporan trip, kendaraan, sopir, trayek
- Export Excel/PDF

---

## Prioritas P3 — Future / Roadmap

- Native app khusus sopir
- integrasi sistem eksternal
- analytics lanjutan
- predictive ETA
- public API eksternal

---

## Urutan Kerja yang Disarankan

1. Authentication dan role
2. User + sopir + kendaraan
3. Driver-vehicle assignment
4. Route + route points + stops
5. Fare + schedule
6. Trip management
7. GPS API + vehicle location
8. Real-time broadcast
9. Live map admin/user
10. PWA dan offline support
11. Reporting dan dashboard lanjutan

---

## Prinsip Pengembangan untuk Fase Awal

- Mulai dari data master yang valid
- Pastikan trip berjalan dengan logika yang benar
- Fokus pada satu alur utama: admin → sopir → trip → GPS → map
- Hindari fitur UI yang terlalu kompleks sebelum core sistem stabil
- Gunakan model/service pattern sesuai standar Laravel
- Gunakan validasi dan authorization untuk setiap endpoint penting

---

## Deliverable Minimal MVP

Sistem dianggap layak untuk lanjut ke fase berikutnya jika sudah bisa:

- admin login dan mengelola master data
- sopir login dan mulai trip
- GPS dikirim dan tersimpan
- admin/user melihat posisi kendaraan di peta real-time
- trip selesai dan histori tersimpan

Itu adalah target MVP yang paling penting.
