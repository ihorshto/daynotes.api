<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationEntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'user_id' => $this->user_id,
            'morning_time' => $this->morning_time,
            'afternoon_time' => $this->afternoon_time,
            'evening_time' => $this->evening_time,
            'morning_enabled' => $this->morning_enabled,
            'afternoon_enabled' => $this->afternoon_enabled,
            'evening_enabled' => $this->evening_enabled,
            'timezone' => $this->timezone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
