<?php

declare(strict_types=1);

use App\Models\MoodEntry;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

describe('Delete Mood Entry', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
        $this->moodEntry = MoodEntry::factory()->create(['user_id' => $this->user->id]);
    });

    it('success in deleting mood entry', function (): void {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson(route('mood-entries.destroy', ['mood_entry' => $this->moodEntry->id]));

        $response->assertNoContent();
    });

    it('fails in deleting, throws 403 when another user try to delete a mood entry', function (): void {
        $anotherUser = User::factory()->create();
        Sanctum::actingAs($anotherUser);

        $response = $this->deleteJson(route('mood-entries.destroy', ['mood_entry' => $this->moodEntry->id]));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    });

    it('fails in deleting, throws 401 when an unauthorized user', function (): void {
        $response = $this->deleteJson(route('mood-entries.destroy', ['mood_entry' => $this->moodEntry->id]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    });
});
