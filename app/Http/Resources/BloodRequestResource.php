<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\BloodRequest */
class BloodRequestResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_name' => $this->patient_name,
            'blood_group' => $this->blood_group,
            'city_id' => $this->city_id,
            'hospital' => $this->hospital,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'city' => $this->whenLoaded('city', fn () => new CityResource($this->city)),
            'requester' => $this->whenLoaded('user', fn () => new UserResource($this->user)),
        ];
    }
}
