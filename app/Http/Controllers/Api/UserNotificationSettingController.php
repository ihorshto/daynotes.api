<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\UserNotificationSetting\DeleteUserNotificationSettingAction;
use App\Actions\UserNotificationSetting\StoreUserNotificationSettingAction;
use App\Actions\UserNotificationSetting\UpdateUserNotificationSettingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserNotificationSettingRequest;
use App\Http\Resources\NotificationEntryResource;
use App\Models\UserNotificationSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UserNotificationSettingController extends Controller
{
    public function __construct(
        private readonly UpdateUserNotificationSettingAction $updateNotificationSettingAction,
        private readonly StoreUserNotificationSettingAction $storeUserNotificationSettingAction,
        private readonly DeleteUserNotificationSettingAction $deleteUserNotificationSettingAction,
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
        Gate::authorize('update', $userNotificationSetting);

        $validated = $request->validated();

        $notificationSetting = $this->updateNotificationSettingAction->execute(
            $userNotificationSetting,
            $validated,
        );

        return NotificationEntryResource::make($notificationSetting);
    }

    public function delete(UserNotificationSetting $userNotificationSetting): Response
    {
        Gate::authorize('delete', $userNotificationSetting);

        $this->deleteUserNotificationSettingAction->handle(
            $userNotificationSetting,
        );

        return response()->noContent();
    }
}
