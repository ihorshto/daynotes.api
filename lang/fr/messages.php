<?php

declare(strict_types=1);

return [
    'common' => [
        'not_linked' => '❌ Compte non connecté à Mood Tracker. Utilisez /start pour vous connecter.',
    ],

    'start' => [
        'greeting'       => "👋 *Bonjour !*\n\nCeci est le bot Mood Tracker.\n\nPour vous connecter :\n1. Ouvrez Mood Tracker\n2. Accédez aux Paramètres\n3. Cliquez sur 'Connecter Telegram'\n4. Ouvrez le lien",
        'invalid_code'   => "❌ *Code expiré ou invalide*\n\nVeuillez générer un nouveau code dans les paramètres de Mood Tracker.",
        'user_not_found' => '❌ Utilisateur introuvable.',
        'already_linked' => "⚠️ *Déjà connecté*\n\nVotre compte est déjà connecté à un autre chat Telegram. Veuillez d'abord le déconnecter via les paramètres.",
        'success'        => "✅ *Bienvenue, :username !*\n\nTelegram connecté avec succès à Mood Tracker ! 😊\n\nVous pouvez maintenant enregistrer votre humeur et recevoir des rappels. 🎉",
    ],

    'add' => [
        'how_are_you' => '🎭 *Comment vous sentez-vous ?*',
    ],

    'stats' => [
        'unknown_command' => "❌ Commande inconnue. Disponibles :\n*/stats_daily*\n*/stats_weekly*\n*/stats_monthly*",
        'no_entries'      => 'Aucune entrée pour cette période :(',
        'result'          => "📊 *:period*\nHumeur moyenne : *:average*\nNombre : *:count*\nMin : *:min*\nMax : *:max*",
        'periods'         => [
            'daily'   => "Statistiques d'humeur du jour",
            'weekly'  => "Statistiques d'humeur de la semaine",
            'monthly' => "Statistiques d'humeur du mois",
        ],
    ],

    'analytics' => [
        'unknown_command'     => "❌ Commande inconnue. Disponibles :\n*/analytics_daily*\n*/analytics_weekly*\n*/analytics_monthly*",
        'generating'          => '🧠 Génération des analyses pour :period...',
        'service_unavailable' => '⚠️ Le service d\'analyse est indisponible.',
        'unavailable'         => 'Analyses indisponibles.',
        'periods'             => [
            'daily'   => 'jour',
            'weekly'  => 'semaine',
            'monthly' => 'mois',
        ],
    ],

    'unlink' => [
        'not_linked' => "❌ *Non connecté*\n\nVotre compte n'est connecté à aucun chat Telegram.",
        'success'    => "✅ *Déconnecté avec succès*\n\nVotre compte a été déconnecté de ce chat Telegram.",
    ],

    'notifications' => [
        'reminder'         => '⏰ C\'est l\'heure d\'enregistrer votre humeur 😊',
        'add_mood_button'  => '📝 Ajouter humeur',
    ],

    'mood' => [
        'selected'      => "✅ Humeur *:score* enregistrée !\n\n📝 Ajoutez une note ou passez :",
        'skip_button'   => '⏭️ Passer',
        'saved_ack'     => 'Enregistré ! ✅',
        'saved_no_note' => '✅ Humeur enregistrée sans note !',
        'saved'         => 'Humeur enregistrée ✅',
    ],
];
