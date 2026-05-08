<?php

namespace App\Observers;

use App\Models\Tag;
use App\Services\SlugService;

class TagObserver
{
    //
    public function __construct(
        private readonly SlugService $slugService
    ) {}

    public function creating(Tag $tag): void
    {
        if (empty($tag->slug)) {
            $tag->slug = $this->slugService->generateUnique(
                $tag->name,
                new Tag()
            );
        }
    }

    public function updating(Tag $tag): void
    {
        if ($tag->isDirty('name')) {
            $tag->slug = $this->slugService->generateUnique(
                $tag->name,
                new Tag(),
                ignoreId: $tag->id
            );
        }
    }
}
