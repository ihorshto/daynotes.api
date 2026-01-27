<?php

declare(strict_types=1);
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

describe('Update Notification Setting', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->notificationSetting = $this->user->notificationSetting()->create([
            'time'              => '09:00',
            'email_enabled'     => true,
            'telegram_enabled'  => true,
        ]);
    });

    it('success in updating notification setting for user', function (): void {
        Sanctum::actingAs($this->user);

        $updatedData = [
            'time'              => '10:30',
            'email_enabled'     => false,
            'telegram_enabled'  => true,
        ];

        $response = $this->putJson(route('notification-settings.update', $this->notificationSetting->getKey()), $updatedData);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'time',
                    'user_id',
                    'email_enabled',
                    'telegram_enabled',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('user_notification_settings', [
            'id'                => $this->notificationSetting->getKey(),
            'user_id'           => $this->user->id,
            'time'              => $updatedData['time'],
            'email_enabled'     => $updatedData['email_enabled'],
            'telegram_enabled'  => $updatedData['telegram_enabled'],
        ]);
    });

    it('success in updating notification setting with same data', function (): void {
        Sanctum::actingAs($this->user);

        $sameData = [
            'time'              => '09:00',
            'email_enabled'     => true,
            'telegram_enabled'  => true,
        ];

        $response = $this->putJson(route('notification-settings.update', $this->notificationSetting->getKey()), $sameData);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'time',
                    'user_id',
                    'email_enabled',
                    'telegram_enabled',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('user_notification_settings', [
            'id'                => $this->notificationSetting->getKey(),
            'user_id'           => $this->user->id,
            'time'              => $sameData['time'],
            'email_enabled'     => $sameData['email_enabled'],
            'telegram_enabled'  => $sameData['telegram_enabled'],
        ]);
    });

    it('fails in updating, throws 422 when notification setting with empty array', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [];

        $response = $this->putJson(route('notification-settings.update', $this->notificationSetting->getKey()), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['time', 'email_enabled', 'telegram_enabled']);
    });

    it('fails in updating, throws 422 when time is invalid format', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'time'              => '10am',
            'email_enabled'     => false,
            'telegram_enabled'  => true,
        ];

        $response = $this->putJson(route('notification-settings.update', $this->notificationSetting->getKey()), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['time']);
    });

    it('fails in updating, throws 404 when notification setting not found', function (): void {
        Sanctum::actingAs($this->user);

        $validData = [
            'time'              => '11:00',
            'email_enabled'     => true,
            'telegram_enabled'  => false,
        ];

        $nonExistentId = $this->notificationSetting->getKey() + 999;

        $response = $this->putJson(route('notification-settings.update', $nonExistentId), $validData);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });

    it("fails in updating, throws 403 when updating another user's notification setting", function (): void {
        $otherUser = User::factory()->create();
        $otherNotificationSetting = $otherUser->notificationSetting()->create([
            'time'              => '08:00',
            'email_enabled'     => false,
            'telegram_enabled'  => false,
        ]);

        Sanctum::actingAs($this->user);

        $validData = [
            'time'              => '12:00',
            'email_enabled'     => true,
            'telegram_enabled'  => true,
        ];

        $response = $this->putJson(route('notification-settings.update', $otherNotificationSetting->getKey()), $validData);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });

    it('fails in updating, throws 401 when user is unauthenticated', function (): void {
        $validData = [
            'time'              => '13:00',
            'email_enabled'     => false,
            'telegram_enabled'  => true,
        ];

        $response = $this->putJson(route('notification-settings.update', $this->notificationSetting->getKey()), $validData);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });

    it('fails in updating, throws 422 when telegram_enabled is not boolean', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'time'              => '10:30',
            'email_enabled'     => false,
            'telegram_enabled'  => 'yes',
        ];

        $response = $this->putJson(route('notification-settings.update', $this->notificationSetting->getKey()), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['telegram_enabled']);
    });

    it('fails in updating, throws 422 when email_enabled is not boolean', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'time'              => '10:30',
            'email_enabled'     => 'no',
            'telegram_enabled'  => true,
        ];

        $response = $this->putJson(route('notification-settings.update', $this->notificationSetting->getKey()), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email_enabled']);
    });

    it('fails in updating, throws 422 when time is empty', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'time'             => '',
            'email_enabled'    => true,
            'telegram_enabled' => false,
        ];

        $response = $this->putJson(route('notification-settings.update', $this->notificationSetting->getKey()), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['time']);
    });
});
