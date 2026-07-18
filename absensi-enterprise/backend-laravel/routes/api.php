<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        $dbStatus = 'disconnected';
    }

    return response()->json([
        'status'    => 'healthy',
        'service'   => 'Laravel Absensi Enterprise',
        'database'  => $dbStatus,
        'timestamp' => now(),
    ]);
});


// Public: login karyawan
Route::post('/login', [AuthController::class, 'login']);

// Protected: butuh token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('/attendances/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendances/clock-out', [AttendanceController::class, 'clockOut']);
    Route::get('/attendances/history', [AttendanceController::class, 'history']);
});