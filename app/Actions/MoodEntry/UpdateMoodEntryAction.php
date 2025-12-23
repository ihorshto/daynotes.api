<?php

declare(strict_types=1);

namespace App\Actions\MoodEntry;

use App\Models\MoodEntry;

class UpdateMoodEntryAction
{
    public function execute(MoodEntry $moodEntry, int $moodScore, ?string $note): void
    {
        $moodEntry->update([
            'mood_score' => $moodScore,
            'note'       => $note,
        ]);
    }
}
