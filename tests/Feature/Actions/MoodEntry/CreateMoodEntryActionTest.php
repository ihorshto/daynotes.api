<?php

declare(strict_types=1);

use App\Enums\MoodScore;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

describe('Create Mood Entry', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
    });

    it('create a mood entry with all required fields', function (): void {
        Sanctum::actingAs($this->user);

        $data = [
            'mood_score' => MoodScore::GOOD->value,
            'note'       => 'Original note',
        ];

        $response = $this->postJson(route('mood-entries.store'), $data);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'mood_score',
                    'note',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('mood_entries', [
            'user_id'    => $this->user->id,
            'mood_score' => MoodScore::GOOD->value,
            'note'       => 'Original note',
        ]);
    });

    it('create a mood entry without note', function (): void {
        Sanctum::actingAs($this->user);

        $data = [
            'mood_score' => MoodScore::NEUTRAL->value,
        ];

        $response = $this->postJson(route('mood-entries.store'), $data);

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'mood_score',
                    'note',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('mood_entries', [
            'user_id'    => $this->user->id,
            'mood_score' => MoodScore::NEUTRAL->value,
            'note'       => null,
        ]);
    });

    it('fails to create a mood entry with invalid mood score', function (): void {
        Sanctum::actingAs($this->user);

        $data = [
            'mood_score' => 10,
            'note'       => 'Original note',
        ];

        $response = $this->postJson(route('mood-entries.store'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['mood_score']);
    });

    it('fails to create a mood entry when mood score is missing', function (): void {
        Sanctum::actingAs($this->user);

        $data = [];

        $response = $this->postJson(route('mood-entries.store'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['mood_score']);
    });

    it('fails to create a mood entry with note exceeding max length', function (): void {
        Sanctum::actingAs($this->user);

        $data = [
            'mood_score' => MoodScore::BAD->value,
            'note'       => str_repeat('A', 2001), // 2001 characters
        ];

        $response = $this->postJson(route('mood-entries.store'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['note']);
    });
});
