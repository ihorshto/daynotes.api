<?php

namespace App\Models;

use App\Enums\MoodScore;
use Illuminate\Database\Eloquent\Model;

class MoodEntry extends Model
{
    protected $table = 'mood_entries';

    protected $fillable = [
        'user_id',
        'mood_score',
        'time_of_day',
        'note',
    ];

    protected $casts = [
        'mood_score' => MoodScore::class,
        'time_of_day' => 'string',
        'user_id' => 'integer',
        'note' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
