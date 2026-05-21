<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PengaduanController as AdminPengaduan;
use App\Http\Controllers\Admin\PetugasController as AdminPetugas;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Masyarakat\DashboardController as MasyarakatDashboard;
use App\Http\Controllers\Masyarakat\PengaduanController as MasyarakatPengaduan;
use App\Http\Controllers\Masyarakat\BeritaController;
use App\Http\Controllers\Masyarakat\ProfilController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboard;
use App\Http\Controllers\Petugas\KlarifikasiController as PetugasKlarifikasi;
use App\Http\Controllers\Petugas\PengaduanController as PetugasPengaduan;
use App\Http\Controllers\Petugas\TanggapanController;
use App\Http\Controllers\Masyarakat\KlarifikasiController as MasyarakatKlarifikasi;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/login'));

Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::prefix('admin')->middleware(['is.admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');

    Route::get('/petugas', [AdminPetugas::class, 'index'])->name('admin.petugas.index');
    Route::get('/petugas/create', [AdminPetugas::class, 'create'])->name('admin.petugas.create');
    Route::post('/petugas', [AdminPetugas::class, 'store'])->name('admin.petugas.store');
    Route::get('/petugas/{id}/edit', [AdminPetugas::class, 'edit'])->name('admin.petugas.edit');
    Route::put('/petugas/{id}', [AdminPetugas::class, 'update'])->name('admin.petugas.update');
    Route::delete('/petugas/{id}', [AdminPetugas::class, 'destroy'])->name('admin.petugas.destroy');

    Route::get('/pengaduan', [AdminPengaduan::class, 'index'])->name('admin.pengaduan.index');
    Route::patch('/pengaduan/{id}/status', [AdminPengaduan::class, 'updateStatus'])->name('admin.pengaduan.status');
    Route::post('/pengaduan/{id}/assign', [AdminPengaduan::class, 'assign'])->name('admin.pengaduan.assign');
    Route::delete('/pengaduan/{id}/force', [AdminPengaduan::class, 'forceDestroy'])->name('admin.pengaduan.force-destroy');
    Route::delete('/pengaduan/{id}', [AdminPengaduan::class, 'destroy'])->name('admin.pengaduan.destroy');
});

Route::prefix('petugas')->middleware(['is.petugas'])->group(function () {
    Route::get('/dashboard', [PetugasDashboard::class, 'index'])->name('petugas.dashboard');
    Route::post('/pengaduan/{id}/tanggapan', [TanggapanController::class, 'store'])->name('petugas.tanggapan.store');
    Route::post('/pengaduan/{id}/assign', [TanggapanController::class, 'assign'])->name('petugas.pengaduan.assign');
    Route::get('/complaints', [PetugasDashboard::class, 'complaints'])->name('petugas.complaints.json');
    Route::post('/pengaduan/{id}/status', [PetugasPengaduan::class, 'updateStatus'])->name('petugas.pengaduan.status');
    Route::post('/pengaduan/{id}/takedown', [PetugasPengaduan::class, 'takedown'])->name('petugas.pengaduan.takedown');
    Route::get('/klarifikasi/{id}', [PetugasKlarifikasi::class, 'index'])->name('petugas.klarifikasi.index');
    Route::post('/klarifikasi/{id}', [PetugasKlarifikasi::class, 'store'])->name('petugas.klarifikasi.store');
});

Route::prefix('masyarakat')->middleware(['is.masyarakat'])->group(function () {
    Route::get('/dashboard', [MasyarakatDashboard::class, 'index'])->name('masyarakat.dashboard');
    Route::get('/berita', [BeritaController::class, 'index'])->name('masyarakat.berita');
    Route::get('/riwayat', [MasyarakatDashboard::class, 'riwayat'])->name('masyarakat.riwayat');
    Route::get('/profil', [ProfilController::class, 'index'])->name('masyarakat.profil');
    Route::get('/pengaduan/create', [MasyarakatPengaduan::class, 'create'])->name('masyarakat.pengaduan.create');
    Route::post('/pengaduan', [MasyarakatPengaduan::class, 'store'])->name('masyarakat.pengaduan.store');
    Route::get('/pengaduan/{id}', [MasyarakatPengaduan::class, 'show'])->name('masyarakat.pengaduan.show');
    Route::post('/klarifikasi/{id}', [MasyarakatKlarifikasi::class, 'store'])->name('masyarakat.klarifikasi.store');
});
