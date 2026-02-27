<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ScreeningController;
use App\Http\Controllers\Api\ChatbotController;
use App\Models\Setting;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Endpoint untuk menerima data screening
Route::post('/screening', [ScreeningController::class, 'store']);

Route::get('/report/{id}', [App\Http\Controllers\Api\ChatbotController::class, 'getReport']);

Route::get('/settings/form-mode', function () {
    // Ambil pengaturan dari database, jika kosong otomatis gunakan 'strict' (mode aman)
    $mode = Setting::where('key', 'form_mode')->value('value') ?? 'strict';
    return response()->json([
        'status' => 'success',
        'mode' => $mode
    ]);
});

// Batasi 15 request per 1 menit per IP Address
Route::middleware('throttle:15,1')->group(function () {
    
    // Route chat AI Anda di sini
    Route::post('/chat', [ChatbotController::class, 'chat']);
    
});