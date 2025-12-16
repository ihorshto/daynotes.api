<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MoodScore;
use App\Models\MoodEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MoodEntry>
 */
class MoodEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'mood_score' => fake()->randomElement(MoodScore::cases())->value,
            'note'       => fake()->optional()->sentence(),
        ];
    }
}
