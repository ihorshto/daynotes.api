<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use HasFactory;

    protected $table = 'user_notification_settings';

    protected $fillable = [
        'user_id',
        'time',
        'email_enabled',
        'telegram_enabled',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id'           => 'integer',
            'time'              => 'string',
            'email_enabled'     => 'boolean',
            'telegram_enabled'  => 'boolean',
        ];
    }
}
