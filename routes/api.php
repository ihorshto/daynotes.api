<?php

declare(strict_types=1);

use App\Actions\UserNotificationSetting\GetUserNotificationSettingAction;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MoodEntryController;
use App\Http\Controllers\Api\MoodStatisticController;
use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserNotificationSettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::patch('/user/lang', [UserController::class, 'updateLang'])->name('user.updateLang');

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::resource('mood-entries', MoodEntryController::class)->except('edit', 'create');

    Route::post('/mood-entries/statistics', [MoodStatisticController::class, 'getStatistics'])->name('mood-entries.getStatistics');

    // Notification Settings
    Route::post('/notification-settings/store', [UserNotificationSettingController::class, 'store'])->name('notification-settings.store');
    Route::put('/notification-settings/{userNotificationSetting}/update', [UserNotificationSettingController::class, 'update'])->name('notification-settings.update');
    Route::delete('/notification-settings/{userNotificationSetting}/delete', [UserNotificationSettingController::class, 'delete'])->name('notification-settings.delete');
    Route::get('/notification-settings/{userNotificationSetting}', GetUserNotificationSettingAction::class)->name('notification-settings.getOne');

    // Telegram
    Route::prefix('telegram')->group(function (): void {
        Route::get('/status', [TelegramController::class, 'status']);
        Route::post('/generate-link', [TelegramController::class, 'generateLinkCode']);
        Route::post('/disconnect', [TelegramController::class, 'disconnect']);
        Route::post('/send-message', [TelegramController::class, 'sendTelegramMessage']);
    });
});

Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);
