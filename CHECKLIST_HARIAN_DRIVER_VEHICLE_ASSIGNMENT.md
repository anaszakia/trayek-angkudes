# Checklist Harian: Driver, Vehicle, dan Assignment

Dokumen ini berfungsi sebagai panduan kerja harian untuk fitur prioritas awal: Driver, Vehicle, dan DriverVehicleAssignment.

---

## Target Hari 1

### Driver
- [ ] Buat migration `drivers`
- [ ] Buat model `Driver`
- [ ] Tambahkan relasi `user()`
- [ ] Tambahkan relasi `assignments()`
- [ ] Validasi field utama: `driver_code`, `nik`, `phone`, `license_number`, `license_type`, `status`
- [ ] Cek migration berjalan tanpa error

### Vehicle
- [ ] Buat migration `vehicles`
- [ ] Buat model `Vehicle`
- [ ] Tambahkan relasi `assignments()`
- [ ] Validasi field utama: `vehicle_code`, `plate_number`, `vehicle_type`, `brand`, `model`, `capacity`, `status`
- [ ] Cek migration berjalan tanpa error

### Assignment
- [ ] Buat migration `driver_vehicle_assignments`
- [ ] Buat model `DriverVehicleAssignment`
- [ ] Tambahkan relasi `driver()` dan `vehicle()`
- [ ] Validasi status: `active`, `ended`, `cancelled`
- [ ] Pastikan unique constraint untuk satu kendaraan aktif

---

## Target Hari 2

### Driver CRUD
- [ ] Buat controller admin untuk driver
- [ ] Buat form request create/update driver
- [ ] Buat view list driver
- [ ] Buat view create driver
- [ ] Buat view edit driver
- [ ] Buat validasi field unik untuk `driver_code` dan `nik`
- [ ] Pastikan struktur view mengikuti pola modul user

### Vehicle CRUD
- [ ] Buat controller admin untuk vehicle
- [ ] Buat form request create/update vehicle
- [ ] Buat view list vehicle
- [ ] Buat view create vehicle
- [ ] Buat view edit vehicle
- [ ] Validasi unik `vehicle_code` dan `plate_number`

### Assignment CRUD
- [ ] Buat controller admin assignment
- [ ] Buat form request assignment
- [ ] Buat view list assignment
- [ ] Buat form assign sopir ke kendaraan
- [ ] Validasi tidak ada kendaraan aktif ganda
- [ ] Validasi tidak ada sopir dengan kendaraan aktif ganda (jika diberlakukan)

---

## Target Hari 3

### Logika Bisnis
- [ ] Menentukan aturan assignment aktif
- [ ] Menentukan status `active` dan `ended`
- [ ] Buat query untuk menampilkan assignment aktif saja
- [ ] Buat query untuk mengecek kendaraan aktif
- [ ] Buat query untuk mengecek sopir aktif

### Test Awal
- [ ] Buat feature test untuk membuat driver
- [ ] Buat feature test untuk membuat vehicle
- [ ] Buat feature test untuk membuat assignment
- [ ] Test validasi satu kendaraan aktif untuk dua sopir
- [ ] Test validasi sopir aktif tidak ganda

---

## Catatan Standar

- Semua query tetap ditulis di Model / Service, bukan di Controller.
- Gunakan validasi form request.
- Gunakan pola view modul user sebagai template utama.
- Gunakan `fillable` di setiap model.
- Gunakan `sync`/`firstOrCreate` dengan bijak untuk data assignment.

---

## Deliverable Awal

Tiga fitur ini dianggap siap lanjut ke tahap berikutnya jika:
- migration berhasil berjalan
- admin bisa menambah driver
- admin bisa menambah vehicle
- admin bisa mengassign kendaraan ke sopir
- validasi logic assignment aktif berfungsi

---

## Tahap Trayek / Route (Status Saat Ini)

### Yang sudah dibuat
- [x] Model `TransportRoute`
- [x] Migration `routes`
- [x] Controller `TransportRouteController`
- [x] View list create edit show trayek
- [x] Relasi `points()` dan `stops()` dalam model route
- [x] Permission `routes.*` sudah masuk dalam `PermissionSeeder`
- [x] Menu `Trayek` ditambahkan ke `MenuSeeder`
- [x] Route CRUD terdaftar di `routes/web.php`
- [x] Test awal `RouteManagementTest` dibuat

### Yang perlu dicek lanjutan
- [ ] Validasi UI pasti sesuai pola modul user
- [ ] Tambahkan CRUD untuk `RoutePoint` dan `RouteStop` jika dibutuhkan untuk operasional penuh
- [ ] Uji akses role `superadmin` dan `driver` terhadap halaman trayek
- [ ] Lanjut ke fitur Tarif, Jadwal, Trip, dan GPS setelah trayek stabil

---

## Urutan Berikutnya

### Prioritas berikutnya
- [ ] Tarif (fare)
- [ ] Jadwal (schedule)
- [ ] Trip (operasional perjalanan)
- [ ] GPS tracking
- [ ] Integrasi monitoring real-time dan log trip

### Standar tetap
- [ ] Semua view mengikuti pola modul user sebagai acuan utama
- [ ] Menu dan permission terus ditambah di seeders
- [ ] Progress dicatat dalam checklist ini setiap tahap
