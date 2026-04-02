<?php

declare(strict_types=1);

return [
    'common' => [
        'not_linked' => '❌ Акаунт не підключено до Mood Tracker. Використайте /start для підключення.',
    ],

    'start' => [
        'greeting'       => "👋 *Привіт!*\n\nЦе Mood Tracker Bot.\n\nДля підключення:\n1. Відкрийте Mood Tracker\n2. Перейдіть в Налаштування\n3. Натисніть 'Підключити Telegram'\n4. Відкрийте посилання",
        'invalid_code'   => "❌ *Код застарів або невалідний*\n\nБудь ласка, згенеруйте новий код у налаштуваннях Mood Tracker.",
        'user_not_found' => '❌ Користувача не знайдено.',
        'already_linked' => "⚠️ *Вже підключено*\n\nВаш акаунт вже підключено до іншого Telegram чату. Спочатку відключіть його через налаштування.",
        'success'        => "✅ *Вітаємо, :username!*\n\nTelegram успішно підключено до Mood Tracker! 😊\n\nТепер ви можете записувати настрій та отримувати нагадування. 🎉",
    ],

    'add' => [
        'how_are_you' => '🎭 *Як ти себе почуваєш?*',
    ],

    'stats' => [
        'unknown_command' => "❌ Невідома команда. Доступні:\n*/stats_daily*\n*/stats_weekly*\n*/stats_monthly*",
        'no_entries'      => 'Немає записів за цей період :(',
        'result'          => "📊 *:period*\nСередній настрій: *:average*\nКількість: *:count*\nМін: *:min*\nМакс: *:max*",
        'periods'         => [
            'daily'   => 'Статистика настрою за день',
            'weekly'  => 'Статистика настрою за тиждень',
            'monthly' => 'Статистика настрою за місяць',
        ],
    ],

    'analytics' => [
        'unknown_command'     => "❌ Невідома команда. Доступні:\n*/analytics_daily*\n*/analytics_weekly*\n*/analytics_monthly*",
        'generating'          => '🧠 Генерую аналітику за :period...',
        'service_unavailable' => '⚠️ Сервіс аналітики недоступний.',
        'unavailable'         => 'Аналітика недоступна.',
        'periods'             => [
            'daily'   => 'день',
            'weekly'  => 'тиждень',
            'monthly' => 'місяць',
        ],
    ],

    'unlink' => [
        'not_linked' => "❌ *Не підключено*\n\nВаш акаунт не підключений до жодного Telegram чату.",
        'success'    => "✅ *Відключено успішно*\n\nВаш акаунт відключено від цього Telegram чату.",
    ],

    'notifications' => [
        'reminder'         => '⏰ Час записати свій настрій 😊',
        'add_mood_button'  => '📝 Додати настрій',
    ],

    'mood' => [
        'selected'      => "✅ Настрій *:score* зафіксовано!\n\n📝 Додай нотатку або пропусти:",
        'skip_button'   => '⏭️ Пропустити',
        'saved_ack'     => 'Збережено! ✅',
        'saved_no_note' => '✅ Настрій збережено без нотатки!',
        'saved'         => 'Настрій збережено ✅',
    ],
];
