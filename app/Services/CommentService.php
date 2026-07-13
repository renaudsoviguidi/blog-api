<?php

namespace App\Services;

use App\Enums\CommentStatusEnum;
use App\Models\Comment;
use App\Models\Post;
use App\Repositories\CommentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class CommentService
{
    public function __construct( private readonly CommentRepository $repository ) {}

    /// ─ Admin

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function moderate(Comment $comment, string $status, ?string $reason = null): Comment
    {
        return DB::transaction(function () use ($comment, $status, $reason) {
            $statusEnum = CommentStatusEnum::from($status);

            $data = [
                'status' => $statusEnum,
                'moderated_by' => Auth::id(),
                'moderated_at' => now(),
            ];

            // Motif uniquement pour rejected
            $data['rejection_reason'] = $statusEnum === CommentStatusEnum::Rejected
                ? $reason
                : null;

            return $this->repository->update($comment, $data);
        });
    }

    /**
     * Marquer comme spam — pas besoin de motif
     */
    public function markAsSpam(Comment $comment): Comment
    {
        return $this->moderate($comment, CommentStatusEnum::Spam->value);
    }

    /**
     * Masquer un commentaire — visible uniquement admin
     */
    public function hide(Comment $comment): Comment
    {
        return $this->moderate($comment, CommentStatusEnum::Hidden->value);
    }

    public function delete(Comment $comment): void
    {
        DB::transaction(fn () => $this->repository->delete($comment));
    }

    public function stats(): array
    {
        $counts = $this->repository->countByStatus();

        return [
            'total' => array_sum($counts),
            'pending' => $counts[CommentStatusEnum::Pending->value]  ?? 0,
            'approved' => $counts[CommentStatusEnum::Approved->value] ?? 0,
            'rejected' => $counts[CommentStatusEnum::Rejected->value] ?? 0,
            'spam' => $counts[CommentStatusEnum::Spam->value]     ?? 0,
            'hidden' => $counts[CommentStatusEnum::Hidden->value]   ?? 0,
        ];
    }

    /// ─ Public

    public function getApprovedByPost(Post $post): Collection
    {
        return $this->repository->getApprovedByPost($post->id);
    }

    public function create(Post $post, array $data): Comment
    {
        return DB::transaction(function () use ($post, $data) {
            $user = Auth::user();

            $parentId = null;
            if (!empty($data['parent_ref'])) {
                $parentId = Comment::where('ref', $data['parent_ref'])->value('id');
            }

            return $this->repository->create([
                'post_id' => $post->id,
                'user_id' => $user?->id,
                'parent_id' => $parentId,
                'content' => $data['content'],
                'guest_name' => $user ? null : ($data['guest_name']  ?? null),
                'guest_email' => $user ? null : ($data['guest_email'] ?? null),
                'status' => CommentStatusEnum::Pending,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        });
    }
}