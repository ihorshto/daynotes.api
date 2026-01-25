<?php

declare(strict_types=1);

namespace App\Actions\UserNotificationSetting;

use App\Models\UserNotificationSetting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Lorisleiva\Actions\Concerns\AsController;

class GetOneUserNotificationSettingAction
{
    use AsController;
    use AuthorizesRequests;

    public function execute(UserNotificationSetting $userNotificationSetting)
    {
        $this->authorize('view', $userNotificationSetting);

        return UserNotificationSetting::query()
            ->where('id', $userNotificationSetting->id)
            ->first();
    }
}
