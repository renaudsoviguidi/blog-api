<?php

namespace App\Repositories;

use App\Enums\CommentStatusEnum;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentRepository
{
    /// ─ Admin : tous les commentaires avec filtres
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        return Comment::query()
            ->with(['user', 'post', 'parent'])
            ->when(
                isset($filters['status']),
                fn ($q) => $q->where('status', $filters['status'])
            )
            ->when(
                isset($filters['post_ref']),
                fn ($q) => $q->whereHas('post',
                    fn ($q) => $q->where('ref', $filters['post_ref'])
                )
            )
            ->when(
                isset($filters['search']),
                fn ($q) => $q->where('content', 'like', '%' . $filters['search'] . '%')
            )
            ->latest()
            ->paginate($perPage);
    }

    /// ─ Public : commentaires approuvés d'un post
    public function getApprovedByPost(int $postId): Collection
    {
        return Comment::query()
            ->with([
                'user', 
                'replies' => fn ($q) => $q
                ->approved()
                ->with('user')
                ->oldest(),
            ])
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->approved()
            ->latest()
            ->get();
    }

    public function create(array $data): Comment
    {
        return Comment::create($data);
    }

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update($data);
        return $comment->fresh();
    }

    public function delete(Comment $comment): void
    {
        $comment->delete();
    }

    public function countByStatus(): array
    {
        return Comment::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    }
}