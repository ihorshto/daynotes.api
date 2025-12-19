<?php

declare(strict_types=1);

use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

describe('Delete Mood Entry', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->moodEntry = MoodEntry::factory()->create(['user_id' => $this->user->id]);
    });

    it('delete a mood entry success', function (): void {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson(route('mood-entries.destroy', ['mood_entry' => $this->moodEntry->id]));

        $response->assertNoContent();
    });

    it('delete a mood entry fails with 403 error', function (): void {
        $anotherUser = User::factory()->create();
        Sanctum::actingAs($anotherUser);

        $response = $this->deleteJson(route('mood-entries.destroy', ['mood_entry' => $this->moodEntry->id]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });
});
