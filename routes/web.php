<?php

use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('barang.index'));

// ─── Auth Routes (Breeze) ─────────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ─── Protected Routes ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard redirect
    Route::get('/dashboard', fn () => redirect()->route('barang.index'))
        ->name('dashboard');

    // ── Barang CRUD ──────────────────────────────────────────────────────────
    Route::resource('barang', BarangController::class);

    // ── Download gambar ──────────────────────────────────────────────────────
    Route::get('barang/{barang}/download', [BarangController::class, 'download'])
        ->name('barang.download');

    // ── Steganography ────────────────────────────────────────────────────────
    Route::post('barang/{barang}/encode', [BarangController::class, 'encodeLSB'])
        ->name('barang.encode');

    Route::get('barang/{barang}/decode', [BarangController::class, 'decodeLSB'])
        ->name('barang.decode');
});
