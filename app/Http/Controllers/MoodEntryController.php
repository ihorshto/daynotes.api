<?php

namespace App\Http\Controllers;

use App\Actions\MoodEntry\CreateMoodEntryAction;
use App\Actions\MoodEntry\DeleteMoodEntryAction;
use App\Actions\MoodEntry\UpdateMoodEntryAction;
use App\Http\Requests\CreateMoodEntryRequest;
use App\Http\Resources\MoodEntryResource;
use App\Models\MoodEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MoodEntryController extends Controller
{
    public function __construct(
        private readonly CreateMoodEntryAction $createMoodEntryAction,
        private readonly UpdateMoodEntryAction $updateMoodEntryAction,
        private readonly DeleteMoodEntryAction $deleteMoodEntryAction
    ) {}

    public function index(Request $request): MoodEntryResource
    {
        return $request->user()->moodEntries()->get()->map(function (MoodEntry $moodEntry) {
            return MoodEntryResource::make($moodEntry);
        });
    }

    public function show(MoodEntry $moodEntry): MoodEntryResource
    {
        return MoodEntryResource::make($moodEntry);
    }

    public function create(CreateMoodEntryRequest $request): MoodEntryResource
    {
        $validated = $request->validated();

        $moodEntry = $this->createMoodEntryAction->execute(
            $request->user(),
            $validated['mood_score'],
            $validated['note'] ?? null
        );

        return MoodEntryResource::make($moodEntry);
    }

    public function update(CreateMoodEntryRequest $request, MoodEntry $moodEntry): MoodEntryResource
    {
        $validated = $request->validated();

        $this->updateMoodEntryAction->execute(
            $moodEntry,
            $validated['mood_score'],
            $validated['note'] ?? null
        );

        return MoodEntryResource::make($moodEntry->fresh());
    }

    public function destroy(MoodEntry $moodEntry): Response
    {
        $this->deleteMoodEntryAction->execute($moodEntry);

        return response()->noContent();
    }
}
