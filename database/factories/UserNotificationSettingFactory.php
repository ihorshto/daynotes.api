<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserNotificationSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserNotificationSetting>
 */
class UserNotificationSettingFactory extends Factory
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
            'time'              => fake()->time(),
            'email_enabled'     => fake()->boolean(),
            'telegram_enabled'  => fake()->boolean(),
        ];
    }
}
