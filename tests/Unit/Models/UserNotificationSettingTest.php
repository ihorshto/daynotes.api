<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserNotificationSetting;

describe('UserNotificationSetting Model', function (): void {
    it('can be created with factory', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create(['user_id' => $user->id]);

        expect($userNotificationSetting)->toBeInstanceOf(UserNotificationSetting::class)
            ->and($userNotificationSetting->user_id)->toBe($user->id);
    });

    it('has correct fillable attributes', function (): void {
        $userNotificationSetting = new UserNotificationSetting;

        expect($userNotificationSetting->getFillable())->toBe([
            'user_id',
            'time',
            'email_enabled',
            'telegram_enabled',
        ]);
    });

    it('casts user_id to integer', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create(['user_id' => $user->id]);

        expect($userNotificationSetting->user_id)->toBeInt();
    });

    it('casts time to string', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create(['user_id' => $user->id]);

        expect($userNotificationSetting->time)->toBeString();
    });

    it('casts email_enabled to boolean', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create(['user_id' => $user->id]);

        expect($userNotificationSetting->email_enabled)->toBeBool();
    });

    it('casts telegram_enabled to boolean', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create(['user_id' => $user->id]);

        expect($userNotificationSetting->telegram_enabled)->toBeBool();
    });

    it('belongs to a user', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create(['user_id' => $user->id]);

        expect($userNotificationSetting->user)->toBeInstanceOf(User::class)
            ->and($userNotificationSetting->user->id)->toBe($user->id);
    });

    it('stores time as string', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create([
            'user_id' => $user->id,
            'time'    => '08:30',
        ]);

        expect($userNotificationSetting->time)->toBeString()
            ->and($userNotificationSetting->time)->toBe('08:30');
    });

    it('stores email_enabled as boolean (true)', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create([
            'user_id'       => $user->id,
            'email_enabled' => true,
        ]);

        expect($userNotificationSetting->email_enabled)->toBeBool()
            ->and($userNotificationSetting->email_enabled)->toBeTrue();
    });

    it('stores email_enabled as boolean (false)', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create([
            'user_id'       => $user->id,
            'email_enabled' => false,
        ]);

        expect($userNotificationSetting->email_enabled)->toBeBool()
            ->and($userNotificationSetting->email_enabled)->toBeFalse();
    });

    it('stores telegram_enabled as boolean (true)', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create([
            'user_id'          => $user->id,
            'telegram_enabled' => true,
        ]);

        expect($userNotificationSetting->telegram_enabled)->toBeBool()
            ->and($userNotificationSetting->telegram_enabled)->toBeTrue();
    });

    it('stores telegram_enabled as boolean (false)', function (): void {
        $user = User::factory()->create();
        $userNotificationSetting = UserNotificationSetting::factory()->create([
            'user_id'          => $user->id,
            'telegram_enabled' => false,
        ]);

        expect($userNotificationSetting->telegram_enabled)->toBeBool()
            ->and($userNotificationSetting->telegram_enabled)->toBeFalse();
    });
});
