<?php

declare(strict_types=1);

namespace App\Actions\MoodEntry;

use App\Enums\StatsPeriod;
use App\Models\MoodEntry;
use App\Models\User;

class GetMoodStatisticAction
{
    public function execute(User $user, StatsPeriod $period): array
    {
        $dateRange = $period->dateRange();

        $moodEntries = MoodEntry::query()
            ->where('user_id', $user->id)
            ->whereBetween('created_at', $dateRange)
            ->get();

        if (count($moodEntries) === 0) {
            return [
                'average' => 0,
                'count'   => 0,
                'min'     => 0,
                'max'     => 0,
            ];
        }

        $averageMood = $moodEntries->avg(fn (MoodEntry $entry) => $entry->mood_score->value);
        $countMood = count($moodEntries);
        $minMood = $moodEntries->min(fn (MoodEntry $entry) => $entry->mood_score->value);
        $maxMood = $moodEntries->max(fn (MoodEntry $entry) => $entry->mood_score->value);

        return [
            'average' => $averageMood,
            'count'   => $countMood,
            'min'     => $minMood,
            'max'     => $maxMood,
        ];
    }
}
