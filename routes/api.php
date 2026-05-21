<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PengaduanController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Laporan anonim — tidak perlu autentikasi
Route::post('/pengaduan', [PengaduanController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/pengaduan', [PengaduanController::class, 'index']);
    Route::get('/pengaduan/{id}', [PengaduanController::class, 'show']);
    Route::get('/pengaduan/{id}/tanggapan', [PengaduanController::class, 'tanggapan']);
});
