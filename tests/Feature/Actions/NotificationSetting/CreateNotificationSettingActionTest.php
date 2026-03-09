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

    it('fails in creating, throws 422 when notification setting with empty array', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [];

        $response = $this->postJson(route('notification-settings.store'), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['time', 'email_enabled', 'telegram_enabled']);
    });

    it('fails in creating, throws 422 when time is invalid format', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'time'              => '9:00',
            'email_enabled'     => true,
            'telegram_enabled'  => false,
        ];

        $response = $this->postJson(route('notification-settings.store'), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['time']);
    });

    it('fails in creating, throws 422 when email_enabled is not boolean', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'time'              => '09:00',
            'email_enabled'     => 'yes',
            'telegram_enabled'  => false,
        ];

        $response = $this->postJson(route('notification-settings.store'), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['email_enabled']);
    });

    it('fails in creating, throws 422 when time is out of range', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'time'              => '25:00',
            'email_enabled'     => true,
            'telegram_enabled'  => false,
        ];

        $response = $this->postJson(route('notification-settings.store'), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['time']);
    });

    it('fails in creating, throws 422 when telegram_enabled is not boolean', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'time'              => '09:00',
            'email_enabled'     => true,
            'telegram_enabled'  => 'no',
        ];

        $response = $this->postJson(route('notification-settings.store', $invalidData));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['telegram_enabled']);
    });

    it('fails in creating, throws 401 when user is unauthenticated', function (): void {
        $data = [
            'time'              => '09:00',
            'email_enabled'     => true,
            'telegram_enabled'  => false,
        ];

        $response = $this->postJson(route('notification-settings.store'), $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });

    it('fails in creating, throws 422 when time is missing', function (): void {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'email_enabled'     => true,
            'telegram_enabled'  => false,
        ];

        $response = $this->postJson(route('notification-settings.store'), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['time']);
    });

    it('success in double creating', function (): void {
        Sanctum::actingAs($this->user);

        $data = [
            'time'              => '09:00',
            'email_enabled'     => true,
            'telegram_enabled'  => false,
        ];

        // First creation
        $response1 = $this->postJson(route('notification-settings.store'), $data);
        $response1->assertSuccessful()
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

        // Attempt to create again with the same data
        $response2 = $this->postJson(route('notification-settings.store'), $data);
        $response2->assertSuccessful()
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

        $this->assertDatabaseCount('user_notification_settings', 2);
    });
});
