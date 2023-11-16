<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AspirantCreationRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'flyer' => asset(Storage::url($this->flyer)),
            'status' => $this->status,
            'status_applied_at' => $this->status_applied_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'constituency' => new ConstituencyResource($this->whenLoaded('constituency')),
            'party' => new PartyResource($this->whenLoaded('party')),
            'position' => new PositionResource($this->whenLoaded('position')),
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
