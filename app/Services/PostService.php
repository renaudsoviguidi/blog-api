<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PostService
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly MediaService   $mediaService,
    ) {}

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function show(Post $post, bool $trackView = false): Post
    {
        if ($trackView && $post->status === "published") {
            $post->increment('views_count');
        }
        return $this->repository->findWithRelations($post);
    }

    public function create(array $data): Post
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['cover_image'])) {
                // Prefix = "post_image_{slug du titre}"
                $prefix = 'post_image_' . Str::slug($data['title']);

                $data['cover_image'] = $this->mediaService->store(
                    file: $data['cover_image'],
                    folder: 'posts',
                    prefix: $prefix,
                    type: 'image'
                );
            }

            $categoryIds = $data['category_ids'] ?? [];
            $tagIds = $data['tag_ids'] ?? [];
            $data['user_id'] = auth('api')->id();

            unset($data['category_ids'], $data['tag_ids']);

            return $this->repository->create($data, $categoryIds, $tagIds);
        });
    }

    public function update(Post $post, array $data): Post
    {
        return DB::transaction(function () use ($post, $data) {
            if (isset($data['cover_image'])) {
                $prefix = 'post_image_' . Str::slug($data['title'] ?? $post->title);

                $data['cover_image'] = $this->mediaService->replace(
                    newFile: $data['cover_image'],
                    oldPath: $post->cover_image,
                    folder: 'posts',
                    prefix: $prefix,
                    type: 'image'
                );
            }

            $categoryIds = $data['category_ids'] ?? [];
            $tagIds = $data['tag_ids'] ?? [];

            unset($data['category_ids'], $data['tag_ids']);

            return $this->repository->update($post, $data, $categoryIds, $tagIds);
        });
    }

    public function publish(Post $post): Post
    {
        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return $post->fresh();
    }

    public function reject(Post $post, string $reason): Post
    {
        $post->update([
            'status' => 'draft',
            'published_at' => null,
            'rejection_reason' => $reason,
        ]);

        return $post->fresh();
    }

    public function delete(Post $post): void
    {
        DB::transaction(function () use ($post) {
            $this->mediaService->delete($post->cover_image, 'image');
            $this->repository->delete($post);
        });
    }
}