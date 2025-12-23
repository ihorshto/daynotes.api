<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\NotificationSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationSetting>
 */
class NotificationSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'           => fake()->numberBetween(1, 10),
            'morning_time'      => fake()->time(),
            'afternoon_time'    => fake()->time(),
            'evening_time'      => fake()->time(),
            'morning_enabled'   => fake()->boolean(),
            'afternoon_enabled' => fake()->boolean(),
            'evening_enabled'   => fake()->boolean(),
            'email_enabled'     => fake()->boolean(),
            'telegram_enabled'  => fake()->boolean(),
        ];
    }
}
