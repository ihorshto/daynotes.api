<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;

class MoodReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $timeOfDay
    ) {}

    public function via($notifiable): array
    {
        $settings = $notifiable->notificationSetting;

        if ($settings) {
            if ($settings->email_enabled) {
                $channels[] = 'mail';
            }

            if ($settings->telegram_enabled && $settings->telegram_chat_id) {
                $channels[] = 'telegram';
            }
        }

        return $channels;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'time_of_day' => $this->timeOfDay,
            'type' => 'mood_reminder',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getTitle())
            ->greeting($this->getTitle())
            ->line($this->getMessage())
            ->action('Record your mood', config('app.frontend_url', 'http://localhost:3000').'/mood/new')
            ->line('Thank you for taking care of your mental health! 💚');
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $url = config('app.frontend_url', 'http://localhost:3000').'/mood/new';

        Log::info('$notifiable' . json_encode($notifiable));

        return TelegramMessage::create()
            ->to($notifiable->notificationSetting->telegram_chat_id)
            ->content('*'.$this->getTitle()."*\n\n".$this->getMessage())
            ->button('Record your mood 📝', $url);
    }

    private function getTitle(): string
    {
        return match ($this->timeOfDay) {
            'morning' => 'Good morning! 🌅',
            'afternoon' => 'Good afternoon! ☀️',
            'evening' => 'Good evening! 🌙',
            default => 'Reminder',
        };
    }

    private function getMessage(): string
    {
        return match ($this->timeOfDay) {
            'morning' => 'How are you feeling today?',
            'afternoon' => 'How is your day going?',
            'evening' => 'How was your day?',
            default => 'Time to record your mood!',
        };
    }
}
