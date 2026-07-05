<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GeofenceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\SettingController;

// Rute untuk Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute untuk Admin yang Sudah Login (Diproteksi)
Route::middleware('auth')->group(function () {
    // Rute Utama (Dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () { return redirect()->route('dashboard'); }); // Arahkan root ke dashboard
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rute Manajemen Admin (Dikelompokkan agar rapi)
    Route::prefix('admin')->group(function () {
        Route::get('/karyawan', [EmployeeController::class, 'index'])->name('karyawan.index');
        Route::get('/karyawan/tambah', [EmployeeController::class, 'create'])->name('karyawan.create');
        Route::post('/karyawan', [EmployeeController::class, 'store'])->name('karyawan.store');

        Route::get('/karyawan/{employee}/edit', [EmployeeController::class, 'edit'])->name('karyawan.edit');
        Route::put('/karyawan/{employee}', [EmployeeController::class, 'update'])->name('karyawan.update');
        Route::patch('/karyawan/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('karyawan.toggleStatus');

        Route::get('/geofencing', [GeofenceController::class, 'index'])->name('geofencing.index');
        Route::post('/geofencing', [GeofenceController::class, 'update'])->name('geofencing.update');
        
        Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');


        Route::get('/cuti', [LeaveController::class, 'index'])->name('cuti.index');
        Route::post('/cuti', [LeaveController::class, 'store'])->name('cuti.store');
        Route::patch('/cuti/{leave}/approve', [LeaveController::class, 'approve'])->name('cuti.approve');
        Route::patch('/cuti/{leave}/reject', [LeaveController::class, 'reject'])->name('cuti.reject');
        Route::delete('/cuti/{leave}', [LeaveController::class, 'destroy'])->name('cuti.destroy');

        Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan.index');
        Route::post('/pengaturan', [SettingController::class, 'update'])->name('pengaturan.update');
    });
});