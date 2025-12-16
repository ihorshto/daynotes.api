<?php

declare(strict_types=1);

use App\Actions\MoodEntry\CreateMoodEntryAction;
use App\Enums\MoodScore;
use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->action = new CreateMoodEntryAction;
    $this->user = User::factory()->create();
});

it('creates a mood entry with all required fields', function (): void {
    $moodScore = MoodScore::GOOD->value;
    $note = 'Feeling great today!';

    $moodEntry = $this->action->execute($this->user, $moodScore, $note);

    expect($moodEntry)->toBeInstanceOf(MoodEntry::class)
        ->and($moodEntry->user_id)->toBe($this->user->id)
        ->and($moodEntry->mood_score)->toBe(MoodScore::GOOD)
        ->and($moodEntry->note)->toBe($note)
        ->and($moodEntry->exists)->toBeTrue();
});

it('creates a mood entry without a note', function (): void {
    $moodScore = MoodScore::NEUTRAL->value;

    $moodEntry = $this->action->execute($this->user, $moodScore, null);

    expect($moodEntry)->toBeInstanceOf(MoodEntry::class)
        ->and($moodEntry->user_id)->toBe($this->user->id)
        ->and($moodEntry->mood_score)->toBe(MoodScore::NEUTRAL)
        ->and($moodEntry->note)->toBeNull()
        ->and($moodEntry->exists)->toBeTrue();
});

it('persists the mood entry to the database', function (): void {
    $moodScore = MoodScore::VERY_GOOD->value;
    $note = 'Amazing day!';

    $moodEntry = $this->action->execute($this->user, $moodScore, $note);

    $this->assertDatabaseHas('mood_entries', [
        'id'         => $moodEntry->id,
        'user_id'    => $this->user->id,
        'mood_score' => $moodScore,
        'note'       => $note,
    ]);
});

it('creates mood entries with different mood scores', function (int $moodScore): void {
    $moodEntry = $this->action->execute($this->user, $moodScore, null);

    expect($moodEntry->mood_score->value)->toBe($moodScore);
})->with([
    'very bad'  => MoodScore::VERY_BAD->value,
    'bad'       => MoodScore::BAD->value,
    'neutral'   => MoodScore::NEUTRAL->value,
    'good'      => MoodScore::GOOD->value,
    'very good' => MoodScore::VERY_GOOD->value,
]);

it('associates the mood entry with the correct user', function (): void {
    $anotherUser = User::factory()->create();
    $moodScore = MoodScore::GOOD->value;

    $moodEntry = $this->action->execute($anotherUser, $moodScore, null);

    expect($moodEntry->user_id)->toBe($anotherUser->id)
        ->and($moodEntry->user_id)->not->toBe($this->user->id);
});

it('creates a mood entry with an empty string note', function (): void {
    $moodScore = MoodScore::NEUTRAL->value;
    $note = '';

    $moodEntry = $this->action->execute($this->user, $moodScore, $note);

    expect($moodEntry->note)->toBe('')
        ->and($moodEntry->exists)->toBeTrue();
});

it('creates a mood entry with a long note', function (): void {
    $moodScore = MoodScore::GOOD->value;
    $note = str_repeat('This is a long note. ', 50);

    $moodEntry = $this->action->execute($this->user, $moodScore, $note);

    expect($moodEntry->note)->toBe($note)
        ->and($moodEntry->exists)->toBeTrue();
});

it('creates multiple mood entries for same user', function (): void {
    $entry1 = $this->action->execute($this->user, MoodScore::GOOD->value, 'First entry');
    $entry2 = $this->action->execute($this->user, MoodScore::BAD->value, 'Second entry');
    $entry3 = $this->action->execute($this->user, MoodScore::NEUTRAL->value, 'Third entry');

    expect(MoodEntry::query()->where('user_id', $this->user->id)->count())->toBe(3)
        ->and($entry1->id)->not->toBe($entry2->id)
        ->and($entry2->id)->not->toBe($entry3->id)
        ->and($entry1->note)->toBe('First entry')
        ->and($entry2->note)->toBe('Second entry')
        ->and($entry3->note)->toBe('Third entry');
});

it('maintains data integrity when creating entry with boundary mood scores', function (): void {
    $minEntry = $this->action->execute($this->user, MoodScore::VERY_BAD->value, 'Minimum score');
    $maxEntry = $this->action->execute($this->user, MoodScore::VERY_GOOD->value, 'Maximum score');

    expect($minEntry->mood_score->value)->toBe(1)
        ->and($maxEntry->mood_score->value)->toBe(5)
        ->and($minEntry->exists)->toBeTrue()
        ->and($maxEntry->exists)->toBeTrue();
});

// Failure tests

it('throws an exception when creating mood entry with invalid mood score', function (int $invalidScore): void {
    $this->action->execute($this->user, $invalidScore, null);
})->with([
    'zero'          => 0,
    'negative'      => -1,
    'above maximum' => 6,
    'large number'  => 100,
])->throws(ValueError::class);

it('does not create mood entry in database when invalid mood score is provided', function (): void {
    $initialCount = MoodEntry::query()->count();
    $moodEntry = null;

    try {
        $moodEntry = $this->action->execute($this->user, 0, 'Invalid mood score');
    } catch (ValueError) {
        // Exception is expected, do nothing
    }

    expect($moodEntry)->toBeNull()
        ->and(MoodEntry::query()->count())->toBe($initialCount);
});

it('throws an exception when user_id is null', function (): void {
    $userWithoutId = new User;

    $this->action->execute($userWithoutId, MoodScore::GOOD->value, 'Test note');
})->throws(QueryException::class);
