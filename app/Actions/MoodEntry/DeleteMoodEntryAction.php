<?php

declare(strict_types=1);

namespace App\Actions\MoodEntry;

use App\Models\MoodEntry;

class DeleteMoodEntryAction
{
    public function execute(MoodEntry $moodEntry): void
    {
        $moodEntry->delete();
    }
}
