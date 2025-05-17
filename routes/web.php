<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::controller(ScheduleController::class)->group(function () {
        Route::get('/schedules', 'index')->name('schedules');
        Route::get('/schedules/add', 'tambahMataKuliah');
        Route::post('/schedules/add', 'submitMataKuliah');
        Route::get('/schedules/{matkul}', 'detailMataKuliah');
        Route::post('/schedules/{matkul}/jadwal', 'ubahJadwal');
    });
    Route::controller(ActivityController::class)->group(function () {
        Route::get('/activities', 'index')->name('activities');
        Route::post('/activities/generate', 'generate');
    });
});

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
// Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
