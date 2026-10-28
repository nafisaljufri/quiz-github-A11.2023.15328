<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\VerifikasiPembayaranController;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Dokter\PeriksaPasienController;
use App\Http\Controllers\Dokter\RiwayatPasienController;
use App\Http\Controllers\Pasien\DashboardController;
use App\Http\Controllers\Pasien\PembayaranController;
use App\Http\Controllers\Pasien\PoliController;
use App\Http\Controllers\Pasien\RiwayatController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Admin\ExportAdminController;
use App\Http\Controllers\Dokter\ExportDokterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/* ===================== ADMIN ===================== */
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('polis', AdminPoliController::class);
    Route::resource('dokter', DokterController::class);
    Route::resource('pasien', PasienController::class);
    Route::resource('obat', ObatController::class)->except(['show']);

    Route::get('/pembayaran', [VerifikasiPembayaranController::class, 'index'])->name('admin.pembayaran.index');
    Route::get('/pembayaran/{id}', [VerifikasiPembayaranController::class, 'show'])->name('admin.pembayaran.show');
    Route::post('/pembayaran/{id}/konfirmasi', [VerifikasiPembayaranController::class, 'konfirmasi'])->name('admin.pembayaran.konfirmasi');

    Route::get('/export/dokter', [ExportAdminController::class, 'dokter'])->name('admin.export.dokter');
    Route::get('/export/pasien', [ExportAdminController::class, 'pasien'])->name('admin.export.pasien');
    Route::get('/export/obat', [ExportAdminController::class, 'obat'])->name('admin.export.obat');
});

/* ===================== DOKTER ===================== */
Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::get('/dashboard', function () {
        return view('dokter.dashboard');
    })->name('dokter.dashboard');

    Route::resource('jadwal-periksa', JadwalPeriksaController::class)->except(['show']);

    Route::get('/periksa-pasien', [PeriksaPasienController::class, 'index'])->name('periksa-pasien.index');
    Route::post('/periksa-pasien', [PeriksaPasienController::class, 'store'])->name('periksa-pasien.store');
    Route::get('/periksa-pasien/{id}', [PeriksaPasienController::class, 'create'])->name('periksa-pasien.create');

    Route::get('/riwayat-pasien', [RiwayatPasienController::class, 'index'])->name('riwayat-pasien.index');
    Route::get('/riwayat-pasien/{id}', [RiwayatPasienController::class, 'show'])->name('riwayat-pasien.show');

    Route::get('/export/jadwal-periksa', [ExportDokterController::class, 'jadwalPeriksa'])->name('dokter.export.jadwal');
    Route::get('/export/riwayat-pasien', [ExportDokterController::class, 'riwayatPasien'])->name('dokter.export.riwayat');
});

/* ===================== PASIEN ===================== */
Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('pasien.dashboard');

    Route::get('/daftar', [PoliController::class, 'get'])->name('pasien.daftar');
    Route::post('/daftar', [PoliController::class, 'submit'])->name('pasien.daftar.submit');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('pasien.riwayat');
    Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('pasien.riwayat.show');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pasien.pembayaran');
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->name('pasien.pembayaran.show');
    Route::post('/pembayaran/{id}', [PembayaranController::class, 'upload'])->name('pasien.pembayaran.upload');
});

/* ===================== NOTIFIKASI ===================== */
foreach (['admin', 'dokter', 'pasien'] as $role) {
    Route::middleware(['auth', "role:$role"])->prefix($role)->group(function () use ($role) {
        Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name("$role.notifikasi");
        Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name("$role.notifikasi.baca-semua");
        Route::post('/notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])->name("$role.notifikasi.baca");
    });
}

/* ===================== PROFILE (semua role) ===================== */
foreach (['admin', 'dokter', 'pasien'] as $role) {
    Route::middleware(['auth', "role:$role"])->prefix($role)->group(function () use ($role) {
        Route::get('/profile', [ProfileController::class, 'edit'])->name("profile.$role");
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name("profile.$role.password");
    });
}