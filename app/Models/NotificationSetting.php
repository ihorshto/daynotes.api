<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $table = 'notification_settings';

    protected $fillable = [
        'user_id',
        'morning_time',
        'afternoon_time',
        'evening_time',
        'morning_enabled',
        'afternoon_enabled',
        'evening_enabled',
        'email_enabled',
        'telegram_enabled',
        'telegram_chat_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'morning_enabled' => 'boolean',
        'afternoon_enabled' => 'boolean',
        'evening_enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'telegram_enabled' => 'boolean',
        'telegram_chat_id' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
