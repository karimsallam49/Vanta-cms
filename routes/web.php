<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MotorcycleController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\ShowroomController;
use App\Http\Controllers\Admin\TestRideController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

/* ---------- Auth ---------- */

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
});

/* ---------- Admin / CMS ---------- */

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Brand-scoped content — policies enforce assigned-brand access.
    Route::resource('motorcycles', MotorcycleController::class)->except('show');
    Route::resource('offers', OfferController::class)->except('show');
    Route::get('test-rides', [TestRideController::class, 'index'])->name('test-rides.index');
    Route::patch('test-rides/{testRide}', [TestRideController::class, 'update'])->name('test-rides.update');

    // Administrator-only management.
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('brands', BrandController::class)->except('show');
        Route::resource('showrooms', ShowroomController::class)->except('show');
    });
});
