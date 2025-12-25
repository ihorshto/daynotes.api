<?php

declare(strict_types=1);
use App\Models\User;

describe('Update Notification Setting', function (): void {
    beforeEach(function (): void {
        $this->user = User::factory()->create();
    });

    //    it('success in updating notification setting for user', function (): void {
    //        Sanctum::actingAs($this->user);
    //
    //        $notificationSetting = NotificationSetting::factory()->create(['user_id' => $this->user->id]);
    //
    //        $updatedData = [
    //            'morning_time'      => '08:00',
    //            'afternoon_time'    => '13:00',
    //            'evening_time'      => '19:00',
    //            'morning_enabled'   => false,
    //            'afternoon_enabled' => true,
    //            'evening_enabled'   => true,
    //            'email_enabled'     => true,
    //            'telegram_enabled'  => false,
    //        ];
    //
    //        $response = $this->postJson(route('notification-settings.update'), $updatedData);
    //
    //        $response->assertSuccessful()
    //            ->assertJsonStructure([
    //                'data' => [
    //                    'id',
    //                    'morning_time',
    //                    'afternoon_time',
    //                    'evening_time',
    //                    'morning_enabled',
    //                    'afternoon_enabled',
    //                    'evening_enabled',
    //                    'email_enabled',
    //                    'telegram_enabled',
    //                    'created_at',
    //                    'updated_at',
    //                ],
    //            ]);
    //
    //        $this->assertDatabaseHas('notification_settings', [
    //            'id'                => $notificationSetting->getKey(),
    //            'user_id'           => $notificationSetting->user->id,
    //            'morning_time'      => $updatedData['morning_time'],
    //            'afternoon_time'    => $updatedData['afternoon_time'],
    //            'evening_time'      => $updatedData['evening_time'],
    //            'morning_enabled'   => $updatedData['morning_enabled'],
    //            'afternoon_enabled' => $updatedData['afternoon_enabled'],
    //            'evening_enabled'   => $updatedData['evening_enabled'],
    //            'email_enabled'     => $updatedData['email_enabled'],
    //            'telegram_enabled'  => $updatedData['telegram_enabled'],
    //        ]);
    //    });
});
