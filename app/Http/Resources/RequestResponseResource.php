<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\RequestResponse */
class RequestResponseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_id' => $this->request_id,
            'donor_id' => $this->donor_id,
            'status' => $this->status,
            'donor' => $this->whenLoaded('donor', function () {
                return DonorResource::make($this->donor);
            }),
        ];
    }
}
