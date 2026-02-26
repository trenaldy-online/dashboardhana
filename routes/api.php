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

// Route untuk fitur Chatbot AI
Route::post('/chat', [ChatbotController::class, 'chat']);

Route::get('/report/{id}', [App\Http\Controllers\Api\ChatbotController::class, 'getReport']);

Route::get('/settings/form-mode', function () {
    // Ambil pengaturan dari database, jika kosong otomatis gunakan 'strict' (mode aman)
    $mode = Setting::where('key', 'form_mode')->value('value') ?? 'strict';
    return response()->json([
        'status' => 'success',
        'mode' => $mode
    ]);
});