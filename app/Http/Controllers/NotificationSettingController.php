<?php

namespace App\Http\Controllers;

use App\Actions\NotificationSetting\UpdateNotificationSettingAction;
use App\Http\Requests\UpdateNotificationSettingRequest;
use App\Http\Resources\NotificationEntryResource;

class NotificationSettingController extends Controller
{
    public function __construct(
        private readonly UpdateNotificationSettingAction $updateNotificationSettingAction
    ) {}

    public function update(UpdateNotificationSettingRequest $request): NotificationEntryResource
    {
        $validated = $request->validated();

        $notificationSetting = $this->updateNotificationSettingAction->execute(
            $request->user(),
            $validated,
        );

        return NotificationEntryResource::make($notificationSetting);
    }
}
