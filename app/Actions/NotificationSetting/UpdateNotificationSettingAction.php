<?php

declare(strict_types=1);

namespace App\Actions\NotificationSetting;

use App\Models\NotificationSetting;
use App\Models\User;

class UpdateNotificationSettingAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(User $user, array $data): NotificationSetting
    {
        $setting = $user->notificationSetting;

        return $user->notificationSetting()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'morning_time'      => $data['morning_time'] ?? $setting?->morning_time ?? '09:00',
                'afternoon_time'    => $data['afternoon_time'] ?? $setting?->afternoon_time ?? '14:00',
                'evening_time'      => $data['evening_time'] ?? $setting?->evening_time ?? '20:00',
                'morning_enabled'   => $data['morning_enabled'] ?? $setting?->morning_enabled ?? true,
                'afternoon_enabled' => $data['afternoon_enabled'] ?? $setting?->afternoon_enabled ?? true,
                'evening_enabled'   => $data['evening_enabled'] ?? $setting?->evening_enabled ?? true,
                'email_enabled'     => $data['email_enabled'] ?? $setting?->email_enabled ?? true,
                'telegram_enabled'  => $data['telegram_enabled'] ?? $setting?->telegram_enabled ?? false,
            ]
        );
    }
}
