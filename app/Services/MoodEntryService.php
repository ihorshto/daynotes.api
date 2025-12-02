<?php

namespace App\Services;

class MoodEntryService
{
    public function filterByTimeOfDay($query, $timeOfDay)
    {
        switch ($timeOfDay) {
            case 'morning':
                $query->whereTime('created_at', '>=', '06:00:00')
                    ->whereTime('created_at', '<', '12:00:00');
                break;
            case 'afternoon':
                $query->whereTime('created_at', '>=', '12:00:00')
                    ->whereTime('created_at', '<', '18:00:00');
                break;
            case 'evening':
                $query->whereTime('created_at', '>=', '18:00:00')
                    ->orWhereTime('created_at', '<', '06:00:00');
                break;
        }

        return $query;
    }
}
