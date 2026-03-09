<?php

declare(strict_types=1);

use App\Enums\MoodScore;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

describe('Create Mood Entry', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
    });

    it('success in creating a mood entry with all required fields', function (): void {
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

    it('success in creating a mood entry without note', function (): void {
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

    it('success in creating a mood entry with empty note', function (): void {
        Sanctum::actingAs($this->user);

        $data = [
            'mood_score' => MoodScore::BAD->value,
            'note'       => '',
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
            'mood_score' => MoodScore::BAD->value,
            'note'       => null,
        ]);
    });

    it('success in creating a mood entry with note value null', function (): void {
        Sanctum::actingAs($this->user);

        $data = [
            'mood_score' => MoodScore::BAD->value,
            'note'       => null,
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
            'mood_score' => MoodScore::BAD->value,
            'note'       => null,
        ]);
    });

    it('fails in creating, throws 422 when invalid mood score', function (): void {
        Sanctum::actingAs($this->user);

        $data = [
            'mood_score' => 10,
            'note'       => 'Original note',
        ];

        $response = $this->postJson(route('mood-entries.store'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['mood_score']);
    });

    it('fails in creating, throws 422 when mood score and note is missing', function (): void {
        Sanctum::actingAs($this->user);

        $data = [];

        $response = $this->postJson(route('mood-entries.store'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['mood_score']);
    });

    it('fails in creating, throws 422 when note exceeding max length', function (): void {
        Sanctum::actingAs($this->user);

        $data = [
            'mood_score' => MoodScore::BAD->value,
            'note'       => fake()->paragraph('200'), // with 200 sentences
        ];

        $response = $this->postJson(route('mood-entries.store'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['note']);
    });
});
