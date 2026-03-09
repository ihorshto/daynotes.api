<?php

declare(strict_types=1);

namespace App\Actions\UserNotificationSetting;

use App\Models\UserNotificationSetting;
use Lorisleiva\Actions\Concerns\AsController;

class DeleteUserNotificationSettingAction
{
    use AsController;

    public function handle(UserNotificationSetting $userNotificationSetting): void
    {
        $userNotificationSetting->delete();
    }
}
