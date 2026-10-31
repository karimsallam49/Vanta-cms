<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\BrandImportController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MotorcycleController;
use App\Http\Controllers\Admin\MotorcycleImportController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ShowroomController;
use App\Http\Controllers\Admin\SiteContentController;
use App\Http\Controllers\Admin\TestRideController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
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

    // UI language + own account.
    Route::get('locale/{locale}', function (Request $request, string $locale) {
        abort_unless(in_array($locale, ['en', 'ar'], true), 404);
        $request->user()->update(['locale' => $locale]);

        return back();
    })->name('locale');
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    /*
     * Every section is gated by `perm:{section}` — the middleware resolves
     * view/create/edit/delete/import/export from the route name. Policies
     * and form requests still enforce the per-brand scope on top.
     */
    Route::middleware('perm:motorcycles')->group(function () {
        Route::get('motorcycles/import', [MotorcycleImportController::class, 'create'])->name('motorcycles.import.create');
        Route::get('motorcycles/import/template', [MotorcycleImportController::class, 'template'])->name('motorcycles.import.template');
        Route::get('motorcycles/export', [MotorcycleImportController::class, 'export'])->name('motorcycles.export');
        Route::post('motorcycles/import/preview', [MotorcycleImportController::class, 'preview'])->name('motorcycles.import.preview');
        Route::post('motorcycles/import/commit', [MotorcycleImportController::class, 'commit'])->name('motorcycles.import.commit');
        Route::resource('motorcycles', MotorcycleController::class)->except('show');
    });

    Route::resource('offers', OfferController::class)->except('show')->middleware('perm:offers');

    Route::get('test-rides', [TestRideController::class, 'index'])->name('test-rides.index')->middleware('perm:test-rides.view');
    Route::get('test-rides/pending-count', [TestRideController::class, 'pendingCount'])->name('test-rides.pending-count')->middleware('perm:test-rides.view');
    Route::get('test-rides/{testRide}', [TestRideController::class, 'show'])->name('test-rides.show')->middleware('perm:test-rides.view');
    Route::patch('test-rides/{testRide}', [TestRideController::class, 'update'])->name('test-rides.update')->middleware('perm:test-rides.edit');

    Route::middleware('perm:brands')->group(function () {
        Route::get('brands/import', [BrandImportController::class, 'create'])->name('brands.import.create');
        Route::get('brands/import/template', [BrandImportController::class, 'template'])->name('brands.import.template');
        Route::get('brands/export', [BrandImportController::class, 'export'])->name('brands.export');
        Route::post('brands/import/preview', [BrandImportController::class, 'preview'])->name('brands.import.preview');
        Route::post('brands/import/commit', [BrandImportController::class, 'commit'])->name('brands.import.commit');
        Route::resource('brands', BrandController::class)->except('show');
    });

    Route::resource('showrooms', ShowroomController::class)->except('show')->middleware('perm:showrooms');
    Route::resource('currencies', CurrencyController::class)->except('show')->middleware('perm:currencies');
    Route::resource('users', UserController::class)->except('show')->middleware('perm:staff');
    Route::resource('roles', RoleController::class)->except('show')->middleware('perm:roles');

    Route::middleware('perm:site-content')->group(function () {
        Route::get('content', [SiteContentController::class, 'index'])->name('content.index');
        Route::get('content/{key}/edit', [SiteContentController::class, 'edit'])->name('content.edit');
        Route::put('content/{key}', [SiteContentController::class, 'update'])->name('content.update');
    });

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index')->middleware('perm:reports.view');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export')->middleware('perm:reports.export');
    Route::get('activity', [ActivityLogController::class, 'index'])->name('activity.index')->middleware('perm:activity.view');
});
