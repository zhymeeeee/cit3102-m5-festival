<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FestivalController;
use Illuminate\Support\Facades\Route;

// Public: no token yet, because these are how you GET a token.
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected: everything else needs "Authorization: Bearer <token>".
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/festivals', [FestivalController::class, 'index']);
    Route::post('/festivals', [FestivalController::class, 'store']);
    Route::get('/festivals/{festival}', [FestivalController::class, 'show']);
    Route::put('/festivals/{festival}', [FestivalController::class, 'update']);
    Route::delete('/festivals/{festival}', [FestivalController::class, 'destroy']);
});
