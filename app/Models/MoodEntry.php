<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MoodScore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodEntry extends Model
{
    use HasFactory;

    protected $table = 'mood_entries';

    protected $fillable = [
        'user_id',
        'mood_score',
        'time_of_day',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'mood_score'  => MoodScore::class,
            'time_of_day' => 'string',
            'user_id'     => 'integer',
            'note'        => 'string',
        ];
    }
}
