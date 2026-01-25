<?php

declare(strict_types=1);
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

describe('Create Notification Setting', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
    });

    it('success in creating notification setting for user', function (): void {
        Sanctum::actingAs($this->user);

        $data = [
            'time'              => '09:00',
            'email_enabled'     => true,
            'telegram_enabled'  => false,
        ];

        $response = $this->postJson(route('notification-settings.store'), $data);

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
            'user_id'           => $this->user->id,
            'time'              => '09:00',
            'email_enabled'     => true,
            'telegram_enabled'  => false,
        ]);
    });

    it('fails in creating notification setting with empty array', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [];

        $response = $this->postJson(route('notification-settings.store'), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['time', 'email_enabled', 'telegram_enabled']);
    });
});
