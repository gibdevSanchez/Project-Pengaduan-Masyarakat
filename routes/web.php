<?php

use App\Http\Controllers\Admin\BeritaController as AdminBerita;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ExpiredDataController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedback;
use App\Http\Controllers\Admin\HealthController;
use App\Http\Controllers\Admin\KlarifikasiController as AdminKlarifikasi;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\NotificationController as AdminNotif;
use App\Http\Controllers\Admin\PengaduanController as AdminPengaduan;
use App\Http\Controllers\Admin\PetugasController as AdminPetugas;
use App\Http\Controllers\Admin\UtilitasController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Masyarakat\DashboardController as MasyarakatDashboard;
use App\Http\Controllers\Masyarakat\FeedbackController as MasyarakatFeedback;
use App\Http\Controllers\Masyarakat\NotificationController as MasyarakatNotif;
use App\Http\Controllers\Masyarakat\PengaduanController as MasyarakatPengaduan;
use App\Http\Controllers\Masyarakat\BeritaController;
use App\Http\Controllers\Masyarakat\ProfilController;
use App\Http\Controllers\Petugas\BeritaController as PetugasBerita;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboard;
use App\Http\Controllers\Petugas\FeedbackController as PetugasFeedback;
use App\Http\Controllers\Petugas\KlarifikasiController as PetugasKlarifikasi;
use App\Http\Controllers\Petugas\NotificationController as PetugasNotif;
use App\Http\Controllers\Petugas\PengaduanController as PetugasPengaduan;
use App\Http\Controllers\Petugas\ProfilController as PetugasProfil;
use App\Http\Controllers\Petugas\TanggapanController;
use App\Http\Controllers\Masyarakat\KlarifikasiController as MasyarakatKlarifikasi;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/login'));

Route::get('/track', [TrackingController::class, 'show'])->name('track');

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

    Route::get('/expired', [ExpiredDataController::class, 'index'])->name('admin.expired.index');
    Route::delete('/expired/all', [ExpiredDataController::class, 'destroyAll'])->name('admin.expired.destroy-all');
    Route::delete('/expired/{id}', [ExpiredDataController::class, 'destroy'])->name('admin.expired.destroy');

    Route::get('/klarifikasi/{id}', [AdminKlarifikasi::class, 'index'])->name('admin.klarifikasi.index');
    Route::post('/klarifikasi/{id}', [AdminKlarifikasi::class, 'store'])->name('admin.klarifikasi.store');
    Route::get('/pengaduan/{id}/anonim-respons', [AdminKlarifikasi::class, 'indexAnonimRespons'])->name('admin.pengaduan.anonim-respons.index');
    Route::post('/pengaduan/{id}/anonim-respons', [AdminKlarifikasi::class, 'storeAnonimRespons'])->name('admin.pengaduan.anonim-respons');

    Route::get('/utilitas', [UtilitasController::class, 'index'])->name('admin.utilitas');
    Route::post('/utilitas', [UtilitasController::class, 'save'])->name('admin.utilitas.save');
    Route::get('/utilitas/peta-data', [UtilitasController::class, 'petaData'])->name('admin.utilitas.peta-data');
    Route::get('/utilitas/logs', [LogController::class, 'index'])->name('admin.utilitas.logs');
    Route::get('/utilitas/health', [HealthController::class, 'index'])->name('admin.utilitas.health');

    Route::get('/notifications', [AdminNotif::class, 'index'])->name('admin.notifications');
    Route::post('/notifications/read', [AdminNotif::class, 'markRead'])->name('admin.notifications.read');

    Route::get('/berita', [AdminBerita::class, 'index'])->name('admin.berita.index');
    Route::get('/berita/create', [AdminBerita::class, 'create'])->name('admin.berita.create');
    Route::post('/berita', [AdminBerita::class, 'store'])->name('admin.berita.store');
    Route::get('/berita/{berita}/edit', [AdminBerita::class, 'edit'])->name('admin.berita.edit');
    Route::put('/berita/{berita}', [AdminBerita::class, 'update'])->name('admin.berita.update');
    Route::delete('/berita/{berita}', [AdminBerita::class, 'destroy'])->name('admin.berita.destroy');

    Route::get('/feedback', [AdminFeedback::class, 'index'])->name('admin.feedback.index');
    Route::get('/feedback/{feedback}', [AdminFeedback::class, 'show'])->name('admin.feedback.show');
    Route::post('/feedback/{feedback}/assign', [AdminFeedback::class, 'assign'])->name('admin.feedback.assign');
    Route::patch('/feedback/{feedback}/penugasan/{penugasan}/status', [AdminFeedback::class, 'updatePenugasanStatus'])->name('admin.feedback.penugasan.status');
    Route::delete('/feedback/{feedback}/penugasan/{penugasan}', [AdminFeedback::class, 'destroyPenugasan'])->name('admin.feedback.penugasan.destroy');
});

Route::prefix('petugas')->middleware(['is.petugas', 'update.last.seen'])->group(function () {
    Route::get('/dashboard', [PetugasDashboard::class, 'index'])->name('petugas.dashboard');
    Route::get('/profil', [PetugasProfil::class, 'index'])->name('petugas.profil');
    Route::put('/profil', [PetugasProfil::class, 'update'])->name('petugas.profil.update');
    Route::post('/pengaduan/{id}/tanggapan', [TanggapanController::class, 'store'])->name('petugas.tanggapan.store');
    Route::post('/pengaduan/{id}/assign', [TanggapanController::class, 'assign'])->name('petugas.pengaduan.assign');
    Route::get('/complaints', [PetugasDashboard::class, 'complaints'])->name('petugas.complaints.json');
    Route::post('/pengaduan/{id}/status', [PetugasPengaduan::class, 'updateStatus'])->name('petugas.pengaduan.status');
    Route::post('/pengaduan/{id}/takedown', [PetugasPengaduan::class, 'takedown'])->name('petugas.pengaduan.takedown');
    Route::get('/klarifikasi/{id}', [PetugasKlarifikasi::class, 'index'])->name('petugas.klarifikasi.index');
    Route::post('/klarifikasi/{id}', [PetugasKlarifikasi::class, 'store'])->name('petugas.klarifikasi.store');
    Route::post('/pengaduan/{id}/tahapan', [PetugasKlarifikasi::class, 'storeTahapan'])->name('petugas.pengaduan.tahapan');
    Route::post('/pengaduan/{id}/selesai', [PetugasKlarifikasi::class, 'storeSelesai'])->name('petugas.pengaduan.selesai');
    Route::get('/pengaduan/{id}/anonim-respons', [PetugasKlarifikasi::class, 'indexAnonimRespons'])->name('petugas.pengaduan.anonim-respons.index');
    Route::post('/pengaduan/{id}/anonim-respons', [PetugasKlarifikasi::class, 'storeAnonimRespons'])->name('petugas.pengaduan.anonim-respons');

    Route::get('/berita', [PetugasBerita::class, 'index'])->name('petugas.berita.index');
    Route::get('/berita/create', [PetugasBerita::class, 'create'])->name('petugas.berita.create');
    Route::post('/berita', [PetugasBerita::class, 'store'])->name('petugas.berita.store');
    Route::get('/berita/{berita}/edit', [PetugasBerita::class, 'edit'])->name('petugas.berita.edit');
    Route::put('/berita/{berita}', [PetugasBerita::class, 'update'])->name('petugas.berita.update');
    Route::delete('/berita/{berita}', [PetugasBerita::class, 'destroy'])->name('petugas.berita.destroy');

    Route::get('/feedback', [PetugasFeedback::class, 'index'])->name('petugas.feedback.index');
    Route::patch('/feedback/{penugasan}/status', [PetugasFeedback::class, 'updateStatus'])->name('petugas.feedback.status');
    Route::patch('/feedback/{penugasan}/hide', [PetugasFeedback::class, 'hide'])->name('petugas.feedback.hide');

    Route::get('/notifications', [PetugasNotif::class, 'index'])->name('petugas.notifications');
    Route::post('/notifications/read', [PetugasNotif::class, 'markRead'])->name('petugas.notifications.read');
});

Route::prefix('masyarakat')->middleware(['is.masyarakat'])->group(function () {
    Route::get('/dashboard', [MasyarakatDashboard::class, 'index'])->name('masyarakat.dashboard');
    Route::get('/berita', [BeritaController::class, 'index'])->name('masyarakat.berita');
    Route::get('/riwayat', [MasyarakatDashboard::class, 'riwayat'])->name('masyarakat.riwayat');
    Route::get('/profil', [ProfilController::class, 'index'])->name('masyarakat.profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('masyarakat.profil.update');
    Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('masyarakat.profil.password');
    Route::get('/pengaduan/create', [MasyarakatPengaduan::class, 'create'])->name('masyarakat.pengaduan.create');
    Route::post('/pengaduan', [MasyarakatPengaduan::class, 'store'])->name('masyarakat.pengaduan.store');
    Route::get('/pengaduan/success/{code}', [MasyarakatPengaduan::class, 'success'])->name('masyarakat.pengaduan.success');
    Route::get('/pengaduan/{id}', [MasyarakatPengaduan::class, 'show'])->name('masyarakat.pengaduan.show');
    Route::post('/klarifikasi/{id}', [MasyarakatKlarifikasi::class, 'store'])->name('masyarakat.klarifikasi.store');
    Route::post('/feedback', [MasyarakatFeedback::class, 'store'])->name('masyarakat.feedback.store');

    Route::get('/notifications', [MasyarakatNotif::class, 'index'])->name('masyarakat.notifications');
    Route::post('/notifications/read', [MasyarakatNotif::class, 'markRead'])->name('masyarakat.notifications.read');
});
