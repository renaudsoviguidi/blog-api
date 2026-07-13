<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository
{
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        return Post::query()
            ->with(['author', 'categories', 'tags'])
            ->when(
                isset($filters['user_id']),
                fn ($q) => $q->where('user_id', $filters['user_id'])
            )
            ->when(
                isset($filters['status']),
                fn ($q) => $q->where('status', $filters['status'])
            )
            ->when(
                isset($filters['search']),
                fn ($q) => $q->where('title', 'like', '%' . $filters['search'] . '%')
            )
            ->when(
                isset($filters['category_ref']),
                fn ($q) => $q->whereHas('categories', fn ($q) =>
                    $q->where('ref', $filters['category_ref'])
                )
            )
            ->when(
                isset($filters['tag_ref']),
                fn ($q) => $q->whereHas('tags', fn ($q) =>
                    $q->where('ref', $filters['tag_ref'])
                )
            )
            ->latest()
            ->paginate($perPage);
    }

    public function findWithRelations(Post $post): Post
    {
        return $post->load(['author', 'categories', 'tags']);
    }

    public function create(array $data, array $categoryIds = [], array $tagIds = []): Post
    {
        $post = Post::create($data);

        if ($categoryIds) {
            $post->categories()->sync($categoryIds);
        }

        if ($tagIds) {
            $post->tags()->sync($tagIds);
        }

        return $post->load(['author', 'categories', 'tags']);
    }

    public function update(Post $post, array $data, array $categoryIds = [], array $tagIds = []): Post
    {
        $post->update($data);
        $post->categories()->sync($categoryIds);
        $post->tags()->sync($tagIds);

        return $post->fresh(['author', 'categories', 'tags']);
    }

    public function delete(Post $post): void
    {
        $post->delete();
    }
}