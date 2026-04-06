<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Telegram\SendTelegramMessage;
use App\Mail\SendEmailNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;

class SendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send telegram or email notification to users based on their notification settings';

    /**
     * Execute the console command.
     */
    public function handle(SendTelegramMessage $sendTelegramMessage): void
    {
        $usersWithNotification = User::query()
            ->whereHas('notificationSetting')
            ->with('notificationSetting')
            ->get();

        foreach ($usersWithNotification as $user) {
            $userCurrentTime = now($user->timezone)->format('H:i');
            $notificationSetting = $user->notificationSetting;

            app()->setLocale($user->lang->value);

            foreach ($notificationSetting as $setting) {
                $settingTime = Date::parse($setting->time)->format('H:i');

                if ($settingTime === $userCurrentTime) {
                    if ($setting->telegram_enabled) {
                        $sendTelegramMessage->execute(
                            (int) $user->telegram_chat_id,
                            __('messages.notifications.reminder'),
                            ['inline_keyboard' => [[
                                ['text' => __('messages.notifications.add_mood_button'), 'callback_data' => 'add_mood'],
                            ]]]
                        );
                    }

                    if ($setting->email_enabled) {
                        Mail::to($user->email)->send(new SendEmailNotification([
                            'name' => $user->name,
                            'time' => $settingTime,
                        ]));
                    }
                }
            }
        }
    }
}
