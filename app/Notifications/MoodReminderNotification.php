<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class MoodReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $timeOfDay,
    ) {}

    /**
     * @return string[]
     */
    public function via($notifiable): array
    {
        $channels = [];
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

    /**
     * @return array<string, string>
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title'       => $this->getTitle(),
            'message'     => $this->getMessage(),
            'time_of_day' => $this->timeOfDay,
            'type'        => 'mood_reminder',
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getTitle())
            ->greeting($this->getTitle())
            ->line($this->getMessage())
            ->action('Record your mood', config('app.frontend_url', 'http://localhost:3000'))
            ->line('Thank you for taking care of your mental health! 💚');
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $url = config('app.frontend_url', 'http://localhost:3000');
        $isLocalhost = str_contains($url, 'localhost') || str_contains($url, '127.0.0.1');

        $message = TelegramMessage::create()
            ->to($notifiable->notificationSetting->telegram_chat_id)
            ->content('*'.$this->getTitle()."*\n\n".$this->getMessage());

        // Only add button if URL is not localhost (Telegram rejects localhost URLs)
        if (! $isLocalhost) {
            $message->button('Record your mood 📝', $url);
        }

        return $message;
    }

    private function getTitle(): string
    {
        return match ($this->timeOfDay) {
            'morning'   => 'Good morning! 🌅',
            'afternoon' => 'Good afternoon! ☀️',
            'evening'   => 'Good evening! 🌙',
            default     => 'Reminder',
        };
    }

    private function getMessage(): string
    {
        return match ($this->timeOfDay) {
            'morning'   => 'How are you feeling today?',
            'afternoon' => 'How is your day going?',
            'evening'   => 'How was your day?',
            default     => 'Time to record your mood!',
        };
    }
}
