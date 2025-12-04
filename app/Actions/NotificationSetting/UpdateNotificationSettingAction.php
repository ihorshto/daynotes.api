<?php

namespace App\Actions\NotificationSetting;

use App\Models\NotificationSetting;
use App\Models\User;

class UpdateNotificationSettingAction
{
    public function execute(User $user, array $data): NotificationSetting
    {
        return $user->notificationSetting()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'morning_time' => $data['morning_time'] ?? $user->notificationSetting->morning_time,
                'afternoon_time' => $data['afternoon_time'] ?? $user->notificationSetting->afternoon_time,
                'evening_time' => $data['evening_time'] ?? $user->notificationSetting->evening_time,
                'morning_enabled' => $data['morning_enabled'] ?? $user->notificationSetting->morning_enabled,
                'afternoon_enabled' => $data['afternoon_enabled'] ?? $user->notificationSetting->afternoon_enabled,
                'evening_enabled' => $data['evening_enabled'] ?? $user->notificationSetting->evening_enabled,
                'timezone' => $data['timezone'] ?? $user->notificationSetting->timezone,
            ]
        );
    }
}
