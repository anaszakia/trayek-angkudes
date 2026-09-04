<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverDashboardController;
use App\Http\Controllers\DriverVehicleAssignmentController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\FareController;
use App\Http\Controllers\GpsTrackingController;
use App\Http\Controllers\RoutePointController;
use App\Http\Controllers\RouteStopController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TransportRouteController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;
use Laravel\Passkeys\Http\Controllers\PasskeyConfirmationController;
use Laravel\Passkeys\Http\Controllers\PasskeyLoginController;
use Laravel\Passkeys\Http\Controllers\PasskeyRegistrationController;

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', fn() => view('auth.register'))->name('register');
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
Route::post('/forgot-password', fn() => back()->with('status', 'Link dikirim!'))->name('password.email');
Route::get('/reset-password/{token?}', fn($token = '') => view('auth.reset-password', compact('token')))->name('password.reset');
Route::post('/reset-password', fn() => redirect()->route('login'))->name('password.update');
Route::get('/otp-verification', fn() => view('auth.otp-verification'))->name('otp.verification');
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Public tracking
Route::get('/tracking', [GpsTrackingController::class, 'publicTracking'])->name('tracking.public');
Route::get('/tracking/latest', [GpsTrackingController::class, 'publicLatest'])->name('tracking.latest');

// Passkeys
Route::get('/passkeys/login/options', [PasskeyLoginController::class, 'index'])
    ->middleware('guest:web')
    ->name('passkey.login-options');
Route::post('/passkeys/login', [PasskeyLoginController::class, 'store'])
    ->middleware('guest:web')
    ->name('passkey.login');

Route::middleware(['auth:web', 'auth.custom'])->group(function () {
    Route::get('/passkeys/confirm/options', [PasskeyConfirmationController::class, 'index'])
        ->name('passkey.confirm-options');
    Route::post('/passkeys/confirm', [PasskeyConfirmationController::class, 'store'])
        ->name('passkey.confirm');
    Route::get('/user/passkeys/options', [PasskeyRegistrationController::class, 'index'])
        ->name('passkey.registration-options');
    Route::post('/user/passkeys', [PasskeyRegistrationController::class, 'store'])
        ->name('passkey.store');
    Route::delete('/user/passkeys/{passkey}', [PasskeyRegistrationController::class, 'destroy'])
        ->name('passkey.destroy');
});

// Protected routes
Route::middleware(['auth.custom', 'auto.logout'])->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Workflow driver
    Route::get('/driver/dashboard', [DriverDashboardController::class, 'dashboard'])->name('driver.dashboard')->middleware('permission:trips.view');
    Route::post('/driver/trips/start', [DriverDashboardController::class, 'start'])->name('driver.trips.start')->middleware('permission:trips.start');
    Route::post('/driver/trips/{trip}/stop', [DriverDashboardController::class, 'stop'])->name('driver.trips.stop')->middleware('permission:trips.stop');
    Route::post('/driver/trips/{trip}/location', [DriverDashboardController::class, 'location'])->name('driver.trips.location')->middleware(['permission:gps.update', 'throttle:30,1']);

    // Menu management - hanya admin
    Route::middleware(['role:superadmin'])->group(function () {
        Route::resource('/menus', MenuController::class);
        Route::resource('/roles', RoleController::class);
        Route::resource('/permissions', PermissionController::class);
    });

    // Keep-alive untuk reset session timeout
    Route::post('/keep-alive', function () {
        session(['last_activity' => now()->timestamp]);
        return response()->json(['status' => 'ok']);
    })->middleware(['auth.custom'])->name('keep.alive');

    // Users dengan permission per aksi
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:users.view');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:users.view');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.edit');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');

    // Driver
    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index')->middleware('permission:drivers.view');
    Route::get('/drivers/create', [DriverController::class, 'create'])->name('drivers.create')->middleware('permission:drivers.create');
    Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store')->middleware('permission:drivers.create');
    Route::get('/drivers/{driver}', [DriverController::class, 'show'])->name('drivers.show')->middleware('permission:drivers.view');
    Route::get('/drivers/{driver}/edit', [DriverController::class, 'edit'])->name('drivers.edit')->middleware('permission:drivers.edit');
    Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update')->middleware('permission:drivers.edit');
    Route::delete('/drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy')->middleware('permission:drivers.delete');

    // Vehicle
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index')->middleware('permission:vehicles.view');
    Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create')->middleware('permission:vehicles.create');
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store')->middleware('permission:vehicles.create');
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show')->middleware('permission:vehicles.view');
    Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit')->middleware('permission:vehicles.edit');
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update')->middleware('permission:vehicles.edit');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy')->middleware('permission:vehicles.delete');

    // Assignment
    Route::get('/assignments', [DriverVehicleAssignmentController::class, 'index'])->name('assignments.index')->middleware('permission:assignments.view');
    Route::get('/assignments/create', [DriverVehicleAssignmentController::class, 'create'])->name('assignments.create')->middleware('permission:assignments.create');
    Route::post('/assignments', [DriverVehicleAssignmentController::class, 'store'])->name('assignments.store')->middleware('permission:assignments.create');
    Route::get('/assignments/{assignment}', [DriverVehicleAssignmentController::class, 'show'])->name('assignments.show')->middleware('permission:assignments.view');
    Route::get('/assignments/{assignment}/edit', [DriverVehicleAssignmentController::class, 'edit'])->name('assignments.edit')->middleware('permission:assignments.edit');
    Route::put('/assignments/{assignment}', [DriverVehicleAssignmentController::class, 'update'])->name('assignments.update')->middleware('permission:assignments.edit');
    Route::delete('/assignments/{assignment}', [DriverVehicleAssignmentController::class, 'destroy'])->name('assignments.destroy')->middleware('permission:assignments.delete');

    // Trayek / Route
    Route::get('/routes', [TransportRouteController::class, 'index'])->name('routes.index')->middleware('permission:routes.view');
    Route::get('/routes/create', [TransportRouteController::class, 'create'])->name('routes.create')->middleware('permission:routes.create');
    Route::post('/routes', [TransportRouteController::class, 'store'])->name('routes.store')->middleware('permission:routes.create');
    Route::get('/routes/{route}', [TransportRouteController::class, 'show'])->name('routes.show')->middleware('permission:routes.view');
    Route::get('/routes/{route}/edit', [TransportRouteController::class, 'edit'])->name('routes.edit')->middleware('permission:routes.edit');
    Route::put('/routes/{route}', [TransportRouteController::class, 'update'])->name('routes.update')->middleware('permission:routes.edit');
    Route::delete('/routes/{route}', [TransportRouteController::class, 'destroy'])->name('routes.destroy')->middleware('permission:routes.delete');

    // Route points dan stops
    Route::get('/route-points', [RoutePointController::class, 'index'])->name('route-points.index')->middleware('permission:route_points.view');
    Route::get('/route-points/create', [RoutePointController::class, 'create'])->name('route-points.create')->middleware('permission:route_points.create');
    Route::post('/route-points', [RoutePointController::class, 'store'])->name('route-points.store')->middleware('permission:route_points.create');
    Route::get('/route-points/{routePoint}', [RoutePointController::class, 'show'])->name('route-points.show')->middleware('permission:route_points.view');
    Route::get('/route-points/{routePoint}/edit', [RoutePointController::class, 'edit'])->name('route-points.edit')->middleware('permission:route_points.edit');
    Route::put('/route-points/{routePoint}', [RoutePointController::class, 'update'])->name('route-points.update')->middleware('permission:route_points.edit');
    Route::delete('/route-points/{routePoint}', [RoutePointController::class, 'destroy'])->name('route-points.destroy')->middleware('permission:route_points.delete');

    Route::get('/route-stops', [RouteStopController::class, 'index'])->name('route-stops.index')->middleware('permission:route_stops.view');
    Route::get('/route-stops/create', [RouteStopController::class, 'create'])->name('route-stops.create')->middleware('permission:route_stops.create');
    Route::post('/route-stops', [RouteStopController::class, 'store'])->name('route-stops.store')->middleware('permission:route_stops.create');
    Route::get('/route-stops/{routeStop}', [RouteStopController::class, 'show'])->name('route-stops.show')->middleware('permission:route_stops.view');
    Route::get('/route-stops/{routeStop}/edit', [RouteStopController::class, 'edit'])->name('route-stops.edit')->middleware('permission:route_stops.edit');
    Route::put('/route-stops/{routeStop}', [RouteStopController::class, 'update'])->name('route-stops.update')->middleware('permission:route_stops.edit');
    Route::delete('/route-stops/{routeStop}', [RouteStopController::class, 'destroy'])->name('route-stops.destroy')->middleware('permission:route_stops.delete');

    // Tarif
    Route::get('/fares', [FareController::class, 'index'])->name('fares.index')->middleware('permission:fares.view');
    Route::get('/fares/create', [FareController::class, 'create'])->name('fares.create')->middleware('permission:fares.create');
    Route::post('/fares', [FareController::class, 'store'])->name('fares.store')->middleware('permission:fares.create');
    Route::get('/fares/{fare}', [FareController::class, 'show'])->name('fares.show')->middleware('permission:fares.view');
    Route::get('/fares/{fare}/edit', [FareController::class, 'edit'])->name('fares.edit')->middleware('permission:fares.edit');
    Route::put('/fares/{fare}', [FareController::class, 'update'])->name('fares.update')->middleware('permission:fares.edit');
    Route::delete('/fares/{fare}', [FareController::class, 'destroy'])->name('fares.destroy')->middleware('permission:fares.delete');

    // Jadwal
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index')->middleware('permission:schedules.view');
    Route::get('/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create')->middleware('permission:schedules.create');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store')->middleware('permission:schedules.create');
    Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show')->middleware('permission:schedules.view');
    Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit')->middleware('permission:schedules.edit');
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update')->middleware('permission:schedules.edit');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy')->middleware('permission:schedules.delete');

    // Trip
    Route::get('/trips', [TripController::class, 'index'])->name('trips.index')->middleware('permission:trips.view');
    Route::get('/trips/create', [TripController::class, 'create'])->name('trips.create')->middleware('permission:trips.start');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store')->middleware('permission:trips.start');
    Route::get('/trips/{trip}', [TripController::class, 'show'])->name('trips.show')->middleware('permission:trips.view');
    Route::get('/trips/{trip}/edit', [TripController::class, 'edit'])->name('trips.edit')->middleware('permission:trips.start');
    Route::put('/trips/{trip}', [TripController::class, 'update'])->name('trips.update')->middleware('permission:trips.start');
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy')->middleware('permission:trips.history');

    // GPS tracking
    Route::get('/gps', [GpsTrackingController::class, 'index'])->name('gps.index')->middleware('permission:gps.view');
    Route::get('/gps/map', [GpsTrackingController::class, 'map'])->name('gps.map')->middleware('permission:gps.view');
    Route::get('/gps/latest', [GpsTrackingController::class, 'latest'])->name('gps.latest')->middleware('permission:gps.view');
    Route::post('/gps', [GpsTrackingController::class, 'store'])->name('gps.store')->middleware('permission:gps.update');
});
