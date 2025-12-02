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
        'note',
    ];

    protected $casts = [
        'mood_score' => MoodScore::class,
        'user_id' => 'integer',
        'note' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
