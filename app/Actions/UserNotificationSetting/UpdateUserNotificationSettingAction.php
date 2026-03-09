<?php

declare(strict_types=1);

namespace App\Actions\UserNotificationSetting;

use App\Models\UserNotificationSetting;

class UpdateUserNotificationSettingAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(UserNotificationSetting $notificationSetting, array $data): UserNotificationSetting
    {
        $notificationSetting->update(
            [
                'time'              => $data['time'],
                'email_enabled'     => $data['email_enabled'],
                'telegram_enabled'  => $data['telegram_enabled'],
            ]
        );

        return $notificationSetting;
    }
}
