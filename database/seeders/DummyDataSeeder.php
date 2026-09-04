<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\DriverVehicleAssignment;
use App\Models\Fare;
use App\Models\GpsTracking;
use App\Models\RoutePoint;
use App\Models\RouteStop;
use App\Models\Role;
use App\Models\TransportRoute;
use App\Models\TransportSchedule;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $driverRole = Role::firstOrCreate(['slug' => 'driver'], ['name' => 'Pengemudi']);
            $userRole = Role::firstOrCreate(['slug' => 'user'], ['name' => 'Pengguna']);
            $driverUsers = $this->seedUsers($driverRole, $userRole);
            $drivers = $this->seedDrivers($driverUsers);
            $vehicles = $this->seedVehicles();
            $this->seedAssignments($drivers, $vehicles);
            $routes = $this->seedRoutes();
            $this->seedPointsAndStops($routes);
            $this->seedFares($routes);
            $this->seedSchedules($routes);
            $trips = $this->seedTrips($drivers, $vehicles, $routes);
            $this->seedGpsTrackings($trips, $vehicles);
        });

        $this->command?->info('Data dummy trayek berhasil dibuat atau diperbarui.');
    }

    private function seedUsers(?Role $driverRole, ?Role $userRole): array
    {
        $users = [];
        $userData = [
            ['name' => 'Budi Santoso', 'email' => 'budi.driver@trayek.test', 'phone' => '081234560001', 'address' => 'Desa Sukamaju', 'role' => $driverRole],
            ['name' => 'Siti Aminah', 'email' => 'siti.driver@trayek.test', 'phone' => '081234560002', 'address' => 'Desa Makmur', 'role' => $driverRole],
            ['name' => 'Andi Pengguna', 'email' => 'andi.user@trayek.test', 'phone' => '081234560003', 'address' => 'Kecamatan Kota', 'role' => $userRole],
        ];

        foreach ($userData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'phone' => $data['phone'],
                    'address' => $data['address'],
                ]
            );

            if ($data['role']) {
                $user->role_id = $data['role']->id;
                $user->save();
                $user->roles()->syncWithoutDetaching([$data['role']->id]);
            }

            $user->forceFill([
                'email_verified_at' => now(),
                'google_id' => 'dummy-google-' . md5($data['email']),
                'avatar' => 'images/avatars/dummy-' . md5($data['email']) . '.jpg',
            ])->save();

            if ($data['role']?->slug === 'driver') {
                $users[] = $user;
            }
        }

        return $users;
    }

    private function seedDrivers(array $driverUsers): array
    {
        $drivers = [];
        $driverData = [
            ['code' => 'DRV-DUMMY-001', 'nik' => '3310010101010001', 'license' => 'SIM-A-0001', 'type' => 'A'],
            ['code' => 'DRV-DUMMY-002', 'nik' => '3310010101010002', 'license' => 'SIM-A-0002', 'type' => 'A'],
        ];

        foreach ($driverData as $index => $data) {
            $drivers[] = Driver::updateOrCreate(
                ['driver_code' => $data['code']],
                [
                    'user_id' => $driverUsers[$index]->id,
                    'nik' => $data['nik'],
                    'phone' => $driverUsers[$index]->phone,
                    'license_number' => $data['license'],
                    'license_type' => $data['type'],
                    'address' => $driverUsers[$index]->address,
                    'photo' => 'images/drivers/dummy-' . ($index + 1) . '.jpg',
                    'status' => 'active',
                ]
            );
        }

        return $drivers;
    }

    private function seedVehicles(): array
    {
        $vehicles = [];
        $vehicleData = [
            ['code' => 'VHC-DUMMY-001', 'plate' => 'K 1001 DA', 'brand' => 'Suzuki', 'model' => 'Carry', 'color' => 'Putih', 'year' => 2022, 'capacity' => 12],
            ['code' => 'VHC-DUMMY-002', 'plate' => 'K 1002 DA', 'brand' => 'Daihatsu', 'model' => 'Gran Max', 'color' => 'Biru', 'year' => 2023, 'capacity' => 12],
        ];

        foreach ($vehicleData as $data) {
            $vehicles[] = Vehicle::updateOrCreate(
                ['vehicle_code' => $data['code']],
                [
                    'plate_number' => $data['plate'],
                    'vehicle_type' => 'Angkutan Desa',
                    'brand' => $data['brand'],
                    'model' => $data['model'],
                    'color' => $data['color'],
                    'year' => $data['year'],
                    'capacity' => $data['capacity'],
                    'owner_name' => 'Koperasi Angkutan Desa',
                    'photo' => 'images/vehicles/dummy-' . ($data['code']) . '.jpg',
                    'status' => 'active',
                ]
            );
        }

        return $vehicles;
    }

    private function seedAssignments(array $drivers, array $vehicles): void
    {
        foreach ($drivers as $index => $driver) {
            DriverVehicleAssignment::updateOrCreate(
                ['vehicle_id' => $vehicles[$index]->id, 'status' => 'active'],
                [
                    'driver_id' => $driver->id,
                    'started_at' => now()->subMonths(2),
                    'ended_at' => null,
                ]
            );
        }
    }

    private function seedRoutes(): array
    {
        return [
            TransportRoute::updateOrCreate(
                ['code' => 'TR-DUMMY-001'],
                ['name' => 'Pati - Tayu', 'route_type' => 'one_way', 'start_point' => 'Terminal Pati', 'end_point' => 'Terminal Tayu', 'distance_km' => 32.50, 'status' => 'active', 'description' => 'Trayek utama penghubung Pati dan Tayu.']
            ),
            TransportRoute::updateOrCreate(
                ['code' => 'TR-DUMMY-002'],
                ['name' => 'Pati - Juwana', 'route_type' => 'round_trip', 'start_point' => 'Terminal Pati', 'end_point' => 'Terminal Juwana', 'distance_km' => 28.75, 'status' => 'active', 'description' => 'Trayek pulang-pergi Pati dan Juwana.']
            ),
        ];
    }

    private function seedPointsAndStops(array $routes): void
    {
        $points = [
            [$routes[0], ['Terminal Pati', 'Margorejo', 'Wedarijaksa', 'Terminal Tayu']],
            [$routes[1], ['Terminal Pati', 'Trangkil', 'Batangan', 'Terminal Juwana']],
        ];

        foreach ($points as [$route, $names]) {
            foreach ($names as $sequence => $name) {
                RoutePoint::updateOrCreate(
                    ['route_id' => $route->id, 'sequence' => $sequence + 1],
                    ['name' => $name, 'latitude' => -6.75 - ($sequence * 0.01), 'longitude' => 111.03 + ($sequence * 0.01), 'is_terminal' => in_array($sequence, [0, count($names) - 1], true)]
                );
                RouteStop::updateOrCreate(
                    ['route_id' => $route->id, 'sequence' => $sequence + 1],
                    ['name' => $name, 'latitude' => -6.75 - ($sequence * 0.01), 'longitude' => 111.03 + ($sequence * 0.01), 'is_active' => true]
                );
            }
        }
    }

    private function seedFares(array $routes): void
    {
        foreach ($routes as $index => $route) {
            $fares = [
                ['type' => 'general', 'name' => 'Umum', 'amount' => $index === 0 ? 5000 : 4500],
                ['type' => 'student', 'name' => 'Pelajar', 'amount' => $index === 0 ? 3000 : 2500],
            ];
            foreach ($fares as $fare) {
                Fare::updateOrCreate(
                    ['fare_code' => 'FARE-DUMMY-00' . ($index + 1) . '-' . strtoupper(substr($fare['type'], 0, 3))],
                    ['route_id' => $route->id, 'name' => $fare['name'], 'passenger_type' => $fare['type'], 'amount' => $fare['amount'], 'currency' => 'IDR', 'effective_from' => now()->startOfYear(), 'effective_to' => now()->endOfYear(), 'status' => 'active', 'description' => 'Tarif dummy untuk pengujian.']
                );
            }
        }
    }

    private function seedSchedules(array $routes): void
    {
        foreach ($routes as $index => $route) {
            TransportSchedule::updateOrCreate(
                ['schedule_code' => 'SCH-DUMMY-00' . ($index + 1)],
                ['route_id' => $route->id, 'day_of_week' => 'Senin - Sabtu', 'departure_time' => '06:00:00', 'arrival_time' => '08:00:00', 'frequency_minutes' => 20, 'status' => 'active', 'description' => 'Jadwal pagi dan siang untuk pengujian.']
            );
        }
    }

    private function seedTrips(array $drivers, array $vehicles, array $routes): array
    {
        $completed = Trip::updateOrCreate(
            ['trip_code' => 'TRIP-DUMMY-001'],
            ['route_id' => $routes[0]->id, 'driver_id' => $drivers[0]->id, 'vehicle_id' => $vehicles[0]->id, 'status' => 'completed', 'started_at' => now()->subHours(4), 'ended_at' => now()->subHours(2), 'total_passengers' => 8, 'notes' => 'Perjalanan dummy selesai.']
        );
        $active = Trip::updateOrCreate(
            ['trip_code' => 'TRIP-DUMMY-002'],
            ['route_id' => $routes[1]->id, 'driver_id' => $drivers[1]->id, 'vehicle_id' => $vehicles[1]->id, 'status' => 'in_progress', 'started_at' => now()->subMinutes(25), 'ended_at' => null, 'total_passengers' => 5, 'notes' => 'Perjalanan dummy aktif untuk monitoring.']
        );

        return [$completed, $active];
    }

    private function seedGpsTrackings(array $trips, array $vehicles): void
    {
        $locations = [
            [$trips[0], $vehicles[0], -6.751234, 111.034210, now()->subHours(3)],
            [$trips[0], $vehicles[0], -6.761234, 111.044210, now()->subHours(2)->subMinutes(30)],
            [$trips[1], $vehicles[1], -6.771234, 111.054210, now()->subMinutes(2)],
        ];

        foreach ($locations as [$trip, $vehicle, $latitude, $longitude, $recordedAt]) {
            GpsTracking::updateOrCreate(
                ['trip_id' => $trip->id, 'recorded_at' => $recordedAt],
                ['vehicle_id' => $vehicle->id, 'latitude' => $latitude, 'longitude' => $longitude, 'speed_kmh' => 32.50, 'heading' => 120, 'accuracy_m' => 8.20]
            );
        }
    }
}
