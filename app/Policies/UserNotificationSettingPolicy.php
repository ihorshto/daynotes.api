<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\UserNotificationSetting;

class UserNotificationSettingPolicy
{
    public function view(User $user, UserNotificationSetting $userNotificationSetting): bool
    {
        return $user->id === $userNotificationSetting->user_id;
    }

    public function update(User $user, UserNotificationSetting $userNotificationSetting): bool
    {
        return $user->id === $userNotificationSetting->user_id;
    }
}
