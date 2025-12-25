<?php

declare(strict_types=1);

namespace App\Actions\NotificationSetting;

use App\Models\User;
use App\Models\UserNotificationSetting;

class StoreUserNotificationSettingAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(User $user, array $data): UserNotificationSetting
    {
        return UserNotificationSetting::query()->create(
            [
                'user_id'           => $user->id,
                'time'              => $data['time'],
                'email_enabled'     => $data['email_enabled'],
                'telegram_enabled'  => $data['telegram_enabled'],
            ]
        );
    }
}
