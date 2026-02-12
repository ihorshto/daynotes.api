<?php

declare(strict_types=1);

namespace App\Actions\UserNotificationSetting;

use App\Models\UserNotificationSetting;
use Lorisleiva\Actions\Concerns\AsController;

class GetUserNotificationSettingAction
{
    use AsController;

    public function handle(UserNotificationSetting $userNotificationSetting): UserNotificationSetting
    {
        return UserNotificationSetting::query()
            ->where('id', $userNotificationSetting->id)
            ->first();
    }
}
