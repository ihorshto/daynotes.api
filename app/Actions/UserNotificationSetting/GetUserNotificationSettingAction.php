<?php

declare(strict_types=1);

namespace App\Actions\UserNotificationSetting;

use App\Http\Resources\NotificationEntryResource;
use App\Models\UserNotificationSetting;
use Lorisleiva\Actions\Concerns\AsController;

class GetUserNotificationSettingAction
{
    use AsController;

    public function handle(UserNotificationSetting $userNotificationSetting): NotificationEntryResource
    {
        return NotificationEntryResource::make($userNotificationSetting);
    }
}
