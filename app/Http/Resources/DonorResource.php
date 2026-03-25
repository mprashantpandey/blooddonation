<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Donor */
class DonorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'blood_group' => $this->blood_group,
            'age' => $this->age,
            'last_donation_date' => $this->last_donation_date?->format('Y-m-d'),
            'is_available' => $this->is_available,
            'is_enabled' => $this->is_enabled,
            'is_verified' => $this->is_verified,
        ];
    }
}
