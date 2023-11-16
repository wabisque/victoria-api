<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AspirantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'flyer' => asset(Storage::url($this->flyer)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_followed' => $this->followers()->where(
                'users.id',
                $request->user()?->id
            )->exists(),
            'aspirant_update_request' => AspirantUpdateRequestResource::collection($this->whenLoaded('aspirantUpdateRequests')),
            'constituency' => new ConstituencyResource($this->whenLoaded('constituency')),
            'party' => new PartyResource($this->whenLoaded('party')),
            'position' => new PositionResource($this->whenLoaded('position')),
            'user' => new UserResource($this->whenLoaded('user'))
        ];
    }
}
