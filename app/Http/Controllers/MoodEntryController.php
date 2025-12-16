<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\MoodEntry\CreateMoodEntryAction;
use App\Actions\MoodEntry\DeleteMoodEntryAction;
use App\Actions\MoodEntry\FilterMoodEntriesAction;
use App\Actions\MoodEntry\UpdateMoodEntryAction;
use App\Http\Requests\CreateMoodEntryRequest;
use App\Http\Resources\MoodEntryResource;
use App\Models\MoodEntry;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class MoodEntryController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly CreateMoodEntryAction $createMoodEntryAction,
        private readonly UpdateMoodEntryAction $updateMoodEntryAction,
        private readonly DeleteMoodEntryAction $deleteMoodEntryAction,
        private readonly FilterMoodEntriesAction $filterMoodEntriesAction,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filteredMoodEntries = $this->filterMoodEntriesAction->execute($request);

        return MoodEntryResource::collection($filteredMoodEntries);
    }

    public function show(MoodEntry $moodEntry): MoodEntryResource
    {
        $this->authorize('view', $moodEntry);

        return MoodEntryResource::make($moodEntry);
    }

    public function store(CreateMoodEntryRequest $request): MoodEntryResource
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
        $this->authorize('update', $moodEntry);

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
        $this->authorize('delete', $moodEntry);

        $this->deleteMoodEntryAction->execute($moodEntry);

        return response()->noContent();
    }
}
