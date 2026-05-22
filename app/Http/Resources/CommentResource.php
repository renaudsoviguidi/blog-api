<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isAdmin = $request->user()?->hasHabilitation('comment.moderate');

        return [
            'ref' => $this->ref,
            'content' => $this->content,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'status_color' => $this->status?->color(),
            'created_at' => $this->created_at?->toIso8601String(),

            // Auteur connecté ou invité
            'author' => $this->whenLoaded('user',
                fn () => [
                    'ref' => $this->user->ref ?? null,
                    'name' => $this->user->name ?? null,
                ],
                fn () => $this->guest_name ? [
                    'ref' => null,
                    'name' => $this->guest_name,
                ] : null
            ),

            // Post associé
            'post' => $this->whenLoaded('post', fn () => [
                'ref' => $this->post->ref,
                'title' => $this->post->title,
            ]),

            // Réponses imbriquées
            'replies' => CommentResource::collection(
                $this->whenLoaded('replies')
            ),

            // Champs admin uniquement
            'guest_email' => $this->when($isAdmin, $this->guest_email),
            'ip_address' => $this->when($isAdmin, $this->ip_address),
            'user_agent' => $this->when($isAdmin, $this->user_agent),
            'rejection_reason' => $this->when($isAdmin, $this->rejection_reason),
            'moderated_at' => $this->when($isAdmin, $this->moderated_at?->toIso8601String()),
            'moderated_by' => $this->when($isAdmin,
                $this->whenLoaded('moderatedBy', fn () => [
                    'ref' => $this->moderatedBy->ref,
                    'name' => $this->moderatedBy->name,
                ])
            ),
        ];
    }
}
