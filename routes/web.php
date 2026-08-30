<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DriverVehicleAssignmentController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TransportRouteController;
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
});
