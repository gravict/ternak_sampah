<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DaftarHargaController;
use App\Http\Controllers\TriviaController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminTransaksiController;
use App\Http\Controllers\Admin\AdminForecastController;
use Illuminate\Support\Facades\Route;

// ==========================================
// AUTH ROUTES
// ==========================================
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// USER ROUTES (Auth Required)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/panduan', fn() => view('user.panduan'))->name('panduan');
    Route::get('/daftar_harga', [DaftarHargaController::class, 'index'])->name('daftar_harga');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/voucher', [VoucherController::class, 'index'])->name('voucher');
    Route::post('/voucher/redeem', [VoucherController::class, 'redeem'])->name('voucher.redeem');
    Route::get('/berita', fn() => view('user.berita'))->name('berita');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/withdraw', [ProfileController::class, 'withdraw'])->name('withdraw');
    Route::post('/trivia/generate', [TriviaController::class, 'generate'])->name('trivia.generate');
});

// ==========================================
// ADMIN ROUTES
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/proses', [AdminTransaksiController::class, 'proses'])->name('proses');
        Route::post('/terima/{id}', [AdminTransaksiController::class, 'terima'])->name('terima');
        Route::post('/tolak/{id}', [AdminTransaksiController::class, 'tolak'])->name('tolak');
        Route::get('/diterima', [AdminTransaksiController::class, 'diterima'])->name('diterima');
        Route::post('/selesaikan/{id}', [AdminTransaksiController::class, 'selesaikan'])->name('selesaikan');
        Route::get('/selesai', [AdminTransaksiController::class, 'selesai'])->name('selesai');
        Route::post('/forecast', [AdminForecastController::class, 'generate'])->name('forecast');
    });
});
