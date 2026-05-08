<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\SlugService;

class PostObserver
{
    //
    public function __construct(
        private readonly SlugService $slugService
    ) {}

    public function creating(Post $post): void
    {
        if (empty($post->slug)) {
            $post->slug = $this->slugService->generateUnique(
                $post->title,
                new Post()
            );
        }
    }

    public function updating(Post $post): void
    {
        if ($post->isDirty('title')) {
            $post->slug = $this->slugService->generateUnique(
                $post->title,
                new Post(),
                ignoreId: $post->id
            );
        }
    }
}
