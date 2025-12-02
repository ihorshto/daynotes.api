<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MoodEntryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/mood-entries/create', [MoodEntryController::class, 'create']);
    Route::post('/mood-entries/{moodEntry}/update', [MoodEntryController::class, 'update']);
    Route::delete('/mood-entries/{moodEntry}/delete', [MoodEntryController::class, 'delete']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
