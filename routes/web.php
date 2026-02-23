<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\MarketingController;

// Halaman Utama akan langsung diarahkan ke login
Route::get('/', function () {
    return redirect('/login');
});

// --- RUTE PUBLIK (LOGIN) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'processLogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTE TERKUNCI (HANYA BISA DIAKSES JIKA SUDAH LOGIN) ---
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/{id}', [DashboardController::class, 'show']);
    Route::get('/analytics', [AnalyticsController::class, 'index']);
    // --- FITUR MARKETING AI ---
    Route::get('/marketing', [MarketingController::class, 'index']);
    Route::post('/marketing/generate', [MarketingController::class, 'generate']);
    Route::post('/marketing/chat', [MarketingController::class, 'chat']); // Untuk follow up chat
    Route::post('/marketing/reset', [MarketingController::class, 'reset']); // Untuk reset tanggal
});