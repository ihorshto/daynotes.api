<?php

declare(strict_types=1);

return [
    'common' => [
        'not_linked' => '❌ Account non collegato a Mood Tracker. Usa /start per collegarlo.',
    ],

    'start' => [
        'greeting'       => "👋 *Ciao!*\n\nQuesto è il bot Mood Tracker.\n\nPer collegare l'account:\n1. Apri Mood Tracker\n2. Vai nelle Impostazioni\n3. Clicca su 'Collega Telegram'\n4. Apri il link",
        'invalid_code'   => "❌ *Codice scaduto o non valido*\n\nGenera un nuovo codice nelle impostazioni di Mood Tracker.",
        'user_not_found' => '❌ Utente non trovato.',
        'already_linked' => "⚠️ *Già collegato*\n\nIl tuo account è già collegato a un'altra chat Telegram. Prima scollegalo dalle impostazioni.",
        'success'        => "✅ *Benvenuto, :username!*\n\nTelegram collegato con successo a Mood Tracker! 😊\n\nOra puoi registrare il tuo umore e ricevere promemoria. 🎉",
    ],

    'add' => [
        'how_are_you' => '🎭 *Come ti senti?*',
    ],

    'stats' => [
        'unknown_command' => "❌ Comando sconosciuto. Disponibili:\n*/stats_daily*\n*/stats_weekly*\n*/stats_monthly*",
        'no_entries'      => 'Nessuna voce per questo periodo :(',
        'result'          => "📊 *:period*\nUmore medio: *:average*\nQuantità: *:count*\nMin: *:min*\nMax: *:max*",
        'periods'         => [
            'daily'   => 'Statistiche umore del giorno',
            'weekly'  => 'Statistiche umore della settimana',
            'monthly' => 'Statistiche umore del mese',
        ],
    ],

    'analytics' => [
        'unknown_command'     => "❌ Comando sconosciuto. Disponibili:\n*/analytics_daily*\n*/analytics_weekly*\n*/analytics_monthly*",
        'generating'          => '🧠 Genero le analisi per :period...',
        'service_unavailable' => '⚠️ Il servizio di analisi non è disponibile.',
        'unavailable'         => 'Analisi non disponibile.',
        'periods'             => [
            'daily'   => 'giorno',
            'weekly'  => 'settimana',
            'monthly' => 'mese',
        ],
    ],

    'unlink' => [
        'not_linked' => "❌ *Non collegato*\n\nIl tuo account non è collegato a nessuna chat Telegram.",
        'success'    => "✅ *Scollegato con successo*\n\nIl tuo account è stato scollegato da questa chat Telegram.",
    ],

    'notifications' => [
        'reminder'         => '⏰ È ora di registrare il tuo umore 😊',
        'add_mood_button'  => '📝 Aggiungi umore',
    ],

    'mood' => [
        'selected'      => "✅ Umore *:score* registrato!\n\n📝 Aggiungi una nota o salta:",
        'skip_button'   => '⏭️ Salta',
        'saved_ack'     => 'Salvato! ✅',
        'saved_no_note' => '✅ Umore salvato senza nota!',
        'saved'         => 'Umore salvato ✅',
    ],
];
