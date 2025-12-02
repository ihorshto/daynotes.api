<?php

namespace App\Actions\MoodEntry;

use App\Models\MoodEntry;
use App\Services\MoodEntryService;
use Illuminate\Support\Collection;

class FilterMoodEntriesAction
{
    public function __construct(
        private MoodEntryService $moodEntryService
    ) {}

    public function execute($request): Collection
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $timeOfDay = $request->query('time_of_day');

        $query = MoodEntry::query()->where('user_id', '=', $request->user()->id);

        // time of day filter: morning, afternoon, evening
        if ($timeOfDay) {
            $this->moodEntryService->filterByTimeOfDay($query, $timeOfDay);
        }

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        return $query->get();
    }
}
