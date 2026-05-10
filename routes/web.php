<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\DwtController;
use Illuminate\Support\Facades\Route;

// ─── Public Routes ────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('barang.index'));

// ─── Auth Routes (Breeze) ─────────────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ─── Protected Routes ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard redirect
    Route::get('/dashboard', fn() => redirect()->route('barang.index'))
        ->name('dashboard');

    Route::get('/about', function () {
        $members = [
            ['name' => 'Ananda Fathurrahmah Lubis',  'role' => 'Project Manager',    'NIRM' => '2023020427'],
            ['name' => 'Master Juanda Sirait',   'role' => 'Fullstack Developer',  'NIRM' => '2023020414'],
            ['name' => 'Muhammad Ridho',  'role' => 'UI UX', 'NIRM' => '2023020414'],
        ];

        return view('about', compact('members'));
    })->name('about');

    // ── Barang CRUD ──────────────────────────────────────────────────────────
    Route::resource('barang', BarangController::class);

    // ── Download gambar ──────────────────────────────────────────────────────
    Route::get('barang/{barang}/download', [BarangController::class, 'download'])
        ->name('barang.download');

    // ── Steganography LSB────────────────────────────────────────────────────────
    Route::post('barang/{barang}/encode', [BarangController::class, 'encodeLSB'])
        ->name('barang.encode');

    Route::get('barang/{barang}/decode', [BarangController::class, 'decodeLSB'])
        ->name('barang.decode');

    // ── Steganography DWT ────────────────────────────────────────────────────
    Route::get('/dwt',                   [DwtController::class, 'index'])->name('dwt.index');
    Route::post('/dwt/encode',           [DwtController::class, 'encode'])->name('dwt.encode');
    Route::post('/dwt/{dwt}/decode',     [DwtController::class, 'decode'])->name('dwt.decode');
    Route::get('/dwt/{dwt}/download',    [DwtController::class, 'download'])->name('dwt.download');
    Route::delete('/dwt/{dwt}',          [DwtController::class, 'destroy'])->name('dwt.destroy');
});
