<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetMoodStatisticRequest;
use App\Models\MoodEntry;

class MoodStatisticController extends Controller
{
    public function getStatistics(GetMoodStatisticRequest $request): float
    {
        $validated = $request->validated();

        $query = MoodEntry::query()
            ->where('user_id', $request->user()->id);

        if (isset($validated['from_date']) && isset($validated['to_date'])) {
            $query->whereBetween('created_at', [$validated['from_date'], $validated['to_date']]);
        }

        return round((float) ($query->avg('mood_score') ?? 0), 2);
    }
}
