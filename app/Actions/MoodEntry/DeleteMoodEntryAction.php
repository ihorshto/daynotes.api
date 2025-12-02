<?php

namespace App\Actions\MoodEntry;

use App\Models\MoodEntry;

class DeleteMoodEntryAction
{
    public function execute(MoodEntry $moodEntry)
    {
        return $moodEntry->delete();
    }
}
