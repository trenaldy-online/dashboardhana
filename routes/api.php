<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ScreeningController;
use App\Http\Controllers\Api\ChatbotController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Endpoint untuk menerima data screening
Route::post('/screening', [ScreeningController::class, 'store']);

// Route untuk fitur Chatbot AI
Route::post('/chat', [ChatbotController::class, 'chat']);

Route::get('/report/{id}', [App\Http\Controllers\Api\ChatbotController::class, 'getReport']);