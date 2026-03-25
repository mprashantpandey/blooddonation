<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'city_id' => $this->city_id,
            'area' => $this->area,
            'referral_code' => $this->referral_code,
            'donor' => $this->whenLoaded('donor', fn () => new DonorResource($this->donor)),
            'city' => $this->whenLoaded('city', fn () => new CityResource($this->city)),
        ];
    }
}
