<?php

declare(strict_types=1);

use App\Enums\MoodScore;
use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

describe('Update Mood Entry', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->moodEntry = MoodEntry::factory()->create([
            'user_id'    => $this->user->id,
            'mood_score' => MoodScore::NEUTRAL->value,
            'note'       => 'Original note',
        ]);
    });

    it('updates mood entry score without changing note', function (): void {
        Sanctum::actingAs($this->user);

        $updatedData = [
            'mood_score' => MoodScore::GOOD->value,
            'note'       => $this->moodEntry->note,
        ];

        $response = $this->patchJson(route('mood-entries.update', $this->moodEntry->getKey()), $updatedData);

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
            'id'          => $this->moodEntry->getKey(),
            'user_id'     => $this->user->id,
            'mood_score'  => MoodScore::GOOD->value,
            'note'        => $this->moodEntry->note,
        ]);
    });

    it('updates mood entry without note', function (): void {
        Sanctum::actingAs($this->user);

        $updatedData = [
            'mood_score' => MoodScore::BAD->value,
            'note'       => '',
        ];

        $response = $this->patchJson(route('mood-entries.update', $this->moodEntry->getKey()), $updatedData);

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
            'id'          => $this->moodEntry->getKey(),
            'user_id'     => $this->user->id,
            'mood_score'  => MoodScore::BAD->value,
            'note'        => null,
        ]);
    });

    it('throws 404 when trying to update a non-existing mood entry', function (): void {
        Sanctum::actingAs($this->user);

        $updatedData = [
            'mood_score' => MoodScore::BAD->value,
            'note'       => 'This should not work',
        ];

        $nonExistingId = 9999;

        $response = $this->patchJson(route('mood-entries.update', $nonExistingId), $updatedData);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    });

    it('throws 403 when trying to update another mood entry', function (): void {
        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $updatedData = [
            'mood_score' => MoodScore::GOOD->value,
            'note'       => 'Updated note',
        ];

        $response = $this->patchJson(route('mood-entries.update', $this->moodEntry->getKey()), $updatedData);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('mood_entries', [
            'id'          => $this->moodEntry->getKey(),
            'user_id'     => $this->user->id,
            'mood_score'  => MoodScore::NEUTRAL->value,
            'note'        => 'Original note',
        ]);
    });

    it('throws error when mood score is out of range', function (): void {
        Sanctum::actingAs($this->user);

        $updatedData = [
            'mood_score' => 10, // Invalid mood score
            'note'       => 'Updated note',
        ];

        $response = $this->patchJson(route('mood-entries.update', $this->moodEntry->getKey()), $updatedData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['mood_score']);

        $this->assertDatabaseHas('mood_entries', [
            'id'          => $this->moodEntry->getKey(),
            'user_id'     => $this->user->id,
            'mood_score'  => MoodScore::NEUTRAL->value,
            'note'        => 'Original note',
        ]);
    });
});
