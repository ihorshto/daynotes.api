<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserNotificationSetting;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

describe('Delete Notification Setting', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();

        $this->notificationSetting = UserNotificationSetting::factory()->create(['user_id' => $this->user->id]);
    });

    it('success in deleting notification setting for user', function (): void {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson(route('notification-settings.delete', $this->notificationSetting->id));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('user_notification_settings', [
            'id' => $this->notificationSetting->id,
        ]);
    });

    it('fails in deleting, throws 403 when user try to modify someone else notification setting', function (): void {
        $this->otherUser = User::factory()->create();

        Sanctum::actingAs($this->otherUser);

        $response = $this->deleteJson(route('notification-settings.delete', $this->notificationSetting->id));

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('user_notification_settings', [
            'id'                => $this->notificationSetting->getKey(),
            'user_id'           => $this->user->id,
            'time'              => $this->notificationSetting->time,
            'email_enabled'     => $this->notificationSetting->email_enabled,
            'telegram_enabled'  => $this->notificationSetting->telegram_enabled,
        ]);
    });

    it('fails in deleting, throws 401 when user is unauthenticated', function (): void {
        $response = $this->deleteJson(route('notification-settings.delete', $this->notificationSetting->id));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertDatabaseHas('user_notification_settings', [
            'id'                => $this->notificationSetting->getKey(),
            'user_id'           => $this->user->id,
            'time'              => $this->notificationSetting->time,
            'email_enabled'     => $this->notificationSetting->email_enabled,
            'telegram_enabled'  => $this->notificationSetting->telegram_enabled,
        ]);
    });
});
