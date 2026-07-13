<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ref' => $this->ref,
            'libelle' => $this->libelle,
            'description' => $this->description,
            'created_at' => $this->created_at?->toIso8601String(),
            'habilitations'  => $this->whenLoaded('habilitations', fn () =>
                $this->habilitations->map(fn ($h) => [
                    'id' => $h->id,
                    'libelle' => $h->libelle,
                    'slug' => $h->slug,
                    'description' => $h->description,
                ])
            ),
            'users_count' => $this->whenCounted('users'),
        ];
    }
}
