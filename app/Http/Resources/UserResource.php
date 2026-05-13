<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "ref" => $this->ref,
            "name" => $this->name,
            "email" => $this->email,
            "roles" => $this->roles->pluck("libelle"),
            "habilitations" => $this->roles->flatMap(fn($role) => $role->habilitations)->pluck('slug')
                ->unique()
                ->values(),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}
