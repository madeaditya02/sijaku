<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KRSController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MataKuliahController;

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
        Route::get('/activities/{activity}', 'show')->name('detail-activity');
        Route::post('/activities/{activity}/confirm', 'confirm')->name('confirm-activity');
        Route::post('/activities/generate', 'generate');
    });
    Route::controller(KRSController::class)->group(function () {
        Route::get('/krs', 'index');
        Route::get('/krs/ajukan', 'ajukan');
        Route::get('/krs/details/{id}', 'detail');
        Route::post('/krs/ajukan/{id}', 'pilihMatkul');
    });
    Route::resource('/students', MahasiswaController::class)->except(['show']);
    Route::resource('/lecturers', DosenController::class)->except(['show']);
    Route::resource('/mata-kuliah', MataKuliahController::class)->except(['show']);
});

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');
// Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
