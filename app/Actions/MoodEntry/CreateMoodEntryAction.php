<?php

declare(strict_types=1);

namespace App\Actions\MoodEntry;

use App\Models\MoodEntry;
use App\Models\User;

class CreateMoodEntryAction
{
    public function execute(User $user, int $moodScore, ?string $note): MoodEntry
    {
        return MoodEntry::query()->create([
            'user_id'    => $user->id,
            'mood_score' => $moodScore,
            'note'       => $note,
        ]);
    }
}
