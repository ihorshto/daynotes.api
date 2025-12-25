<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\NotificationSetting\StoreUserNotificationSettingAction;
use App\Actions\NotificationSetting\UpdateUserNotificationSettingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserNotificationSettingRequest;
use App\Http\Resources\NotificationEntryResource;
use App\Models\UserNotificationSetting;

class UserNotificationSettingController extends Controller
{
    public function __construct(
        private readonly UpdateUserNotificationSettingAction $updateNotificationSettingAction,
        private readonly StoreUserNotificationSettingAction $storeUserNotificationSettingAction,
    ) {}

    public function store(UserNotificationSettingRequest $request): NotificationEntryResource
    {
        $validated = $request->validated();

        $notificationSetting = $this->storeUserNotificationSettingAction->execute(
            $request->user(),
            $validated,
        );

        return NotificationEntryResource::make($notificationSetting);
    }

    public function update(UserNotificationSettingRequest $request, UserNotificationSetting $userNotificationSetting): NotificationEntryResource
    {
        $validated = $request->validated();

        $notificationSetting = $this->updateNotificationSettingAction->execute(
            $userNotificationSetting,
            $validated,
        );

        return NotificationEntryResource::make($notificationSetting);
    }
}
