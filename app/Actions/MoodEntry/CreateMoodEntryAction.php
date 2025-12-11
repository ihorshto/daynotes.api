<?php

namespace App\Actions\MoodEntry;

use App\Models\MoodEntry;
use App\Models\User;

class CreateMoodEntryAction
{
    public function execute(User $user, int $moodScore, ?string $note): MoodEntry
    {
        return $user->moodEntries()->create([
            'mood_score' => $moodScore,
            'note' => $note,
        ]);
    }
}
