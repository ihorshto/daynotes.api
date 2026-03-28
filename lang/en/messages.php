<?php

declare(strict_types=1);

return [
    'common' => [
        'not_linked' => '❌ Account is not connected to Mood Tracker. Use /start to connect.',
    ],

    'start' => [
        'greeting'       => "👋 *Hello!*\n\nThis is Mood Tracker Bot.\n\nTo connect:\n1. Open Mood Tracker\n2. Go to Settings\n3. Click 'Connect Telegram'\n4. Open the link",
        'invalid_code'   => "❌ *Code has expired or is invalid*\n\nPlease generate a new code in Mood Tracker settings.",
        'user_not_found' => '❌ User not found.',
        'already_linked' => "⚠️ *Already connected*\n\nYour account is already connected to another Telegram chat. Please disconnect it first via settings.",
        'success'        => "✅ *Welcome, :username!*\n\nTelegram successfully connected to Mood Tracker! 😊\n\nYou can now record your mood and receive reminders. 🎉",
    ],

    'add' => [
        'how_are_you' => '🎭 *How are you feeling?*',
    ],

    'stats' => [
        'unknown_command' => "❌ Unknown command. Available:\n*/stats_daily*\n*/stats_weekly*\n*/stats_monthly*",
        'no_entries'      => 'No entries for this period :(',
        'result'          => "📊 *:period*\nAverage mood: *:average*\nCount: *:count*\nMin: *:min*\nMax: *:max*",
    ],

    'analytics' => [
        'unknown_command'     => "❌ Unknown command. Available:\n*/analytics_daily*\n*/analytics_weekly*\n*/analytics_monthly*",
        'generating'          => '🧠 Generating analytics for :period...',
        'service_unavailable' => '⚠️ Analytics service is unavailable.',
        'unavailable'         => 'Analytics unavailable.',
    ],

    'unlink' => [
        'not_linked' => "❌ *Not connected*\n\nYour account is not connected to any Telegram chat.",
        'success'    => "✅ *Disconnected successfully*\n\nYour account has been disconnected from this Telegram chat.",
    ],

    'mood' => [
        'selected'      => "✅ Mood *:score* recorded!\n\n📝 Add a note or skip:",
        'skip_button'   => '⏭️ Skip',
        'saved_ack'     => 'Saved! ✅',
        'saved_no_note' => '✅ Mood saved without a note!',
        'saved'         => 'Mood saved ✅',
    ],
];
