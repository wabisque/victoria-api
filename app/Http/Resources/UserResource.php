<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'has_aspirant_creation_request' => $this->aspirantCreationRequests()->whereNull('status')->exists(),
            'has_aspirant_update_request' => $this->aspirant()->whereHas(
                'aspirantUpdateRequests',
                fn($q) => $q->whereNull('status')
            )->exists(),
            'aspirant' => new AspirantResource($this->whenLoaded('aspirant')),
            'role' => new RoleResource($this->whenLoaded('role'))
        ];
    }
}
