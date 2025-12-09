<?php

use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MoodEntryController;
use App\Http\Controllers\NotificationSettingController;
use App\Models\User;
use App\Notifications\MoodReminderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/mood-entries', [MoodEntryController::class, 'index']);
    Route::get('/mood-entries/{moodEntry}', [MoodEntryController::class, 'show']);
    Route::post('/mood-entries/create', [MoodEntryController::class, 'create']);
    Route::post('/mood-entries/{moodEntry}/update', [MoodEntryController::class, 'update']);
    Route::delete('/mood-entries/{moodEntry}/destroy', [MoodEntryController::class, 'destroy']);

    // Notification Settings
    Route::post('/notification-settings/update', [NotificationSettingController::class, 'update'])->name('notification-settings.update');

    // Telegram
    Route::prefix('telegram')->group(function () {
        Route::get('/status', [TelegramController::class, 'status']);
        Route::post('/generate-link', [TelegramController::class, 'generateLinkCode']);
        Route::post('/disconnect', [TelegramController::class, 'disconnect']);
        Route::post('/send-message', [TelegramController::class, 'sendTelegramMessage']);
    });

    Route::get('/test-email', function () {
        $user = User::first();
        $user->notify(new MoodReminderNotification('morning'));

        return 'Email sent!';
    });
});

Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);
