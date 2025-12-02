<?php

namespace App\Actions\MoodEntry;

use App\Models\MoodEntry;
use Illuminate\Support\Collection;

class FilterMoodEntriesAction
{
    public function execute($request): Collection
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $query = MoodEntry::query()->where('user_id', '=', $request->user()->id);

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        return $query->get();
    }
}
