<?php

declare(strict_types=1);

use App\Enums\MoodScore;
use App\Models\MoodEntry;
use App\Models\User;

describe('MoodEntry Model', function (): void {
    it('has correct fillable attributes', function (): void {
        $moodEntry = new MoodEntry;

        expect($moodEntry->getFillable())->toBe([
            'user_id',
            'mood_score',
            'note',
        ]);
    });

    it('casts mood_score to MoodScore enum', function (): void {
        $moodEntry = MoodEntry::factory()->create([
            'mood_score' => MoodScore::GOOD->value,
        ]);

        expect($moodEntry->mood_score)->toBeInstanceOf(MoodScore::class)
            ->and($moodEntry->mood_score)->toBe(MoodScore::GOOD);
    });

    it('casts user_id to integer', function (): void {
        $moodEntry = MoodEntry::factory()->create();

        expect($moodEntry->user_id)->toBeInt();
    });

    it('belongs to a user', function (): void {
        $user = User::factory()->create();
        $moodEntry = MoodEntry::factory()->create(['user_id' => $user->id]);

        expect($moodEntry->user)->toBeInstanceOf(User::class)
            ->and($moodEntry->user->id)->toBe($user->id);
    });

    it('allows null note', function (): void {
        $moodEntry = MoodEntry::factory()->create(['note' => null]);

        expect($moodEntry->note)->toBeNull();
    });

    it('stores note as string', function (): void {
        $moodEntry = MoodEntry::factory()->create(['note' => 'Feeling great today!']);

        expect($moodEntry->note)->toBeString()
            ->and($moodEntry->note)->toBe('Feeling great today!');
    });

    it('can be created with factory', function (): void {
        $moodEntry = MoodEntry::factory()->create();

        expect($moodEntry)->toBeInstanceOf(MoodEntry::class)
            ->and($moodEntry->exists)->toBeTrue();
    });

    it('accepts all valid mood scores', function (MoodScore $moodScore): void {
        $moodEntry = MoodEntry::factory()->create([
            'mood_score' => $moodScore->value,
        ]);

        expect($moodEntry->mood_score)->toBe($moodScore);
    })->with([
        'very bad'  => MoodScore::VERY_BAD,
        'bad'       => MoodScore::BAD,
        'neutral'   => MoodScore::NEUTRAL,
        'good'      => MoodScore::GOOD,
        'very good' => MoodScore::VERY_GOOD,
    ]);
});
