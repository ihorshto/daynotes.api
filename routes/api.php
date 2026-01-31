<?php

declare(strict_types=1);

use App\Actions\Telegram\WebhookAction;
use App\Actions\UserNotificationSetting\GetOneUserNotificationSettingAction;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MoodEntryController;
use App\Http\Controllers\Api\TelegramController;
use App\Http\Controllers\Api\UserNotificationSettingController;
use App\Models\User;
use App\Notifications\MoodReminderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::resource('mood-entries', MoodEntryController::class)->except('edit', 'create');

    // Notification Settings
    Route::post('/notification-settings/store', [UserNotificationSettingController::class, 'store'])->name('notification-settings.store');
    Route::put('/notification-settings/{userNotificationSetting}/update', [UserNotificationSettingController::class, 'update'])->name('notification-settings.update');
    Route::get('/notification-settings/{userNotificationSetting}', GetOneUserNotificationSettingAction::class)->name('notification-settings.getOne');

    // Telegram
    Route::prefix('telegram')->group(function (): void {
        Route::get('/status', [TelegramController::class, 'status']);
        Route::post('/generate-link', [TelegramController::class, 'generateLinkCode']);
        Route::post('/disconnect', [TelegramController::class, 'disconnect']);
        Route::post('/send-message', [TelegramController::class, 'sendTelegramMessage']);
    });

    Route::get('/test-notification', function (): string {
        $user = User::query()->first();
        $user->notify(new MoodReminderNotification('morning'));

        return 'Notifications sent!';
    });
});

Route::post('/telegram/webhook', WebhookAction::class)->name('telegram.webhook');
