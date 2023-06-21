<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChirpResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->user->name,
            ],
            'message' => $this->message,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
