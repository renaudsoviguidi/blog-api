<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModerateCommentRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class CommentController extends Controller
{
    //
    use ApiResponseTrait;

    public function __construct( private readonly CommentService $service ) {}

    /// ─ Admin

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['status', 'post_ref', 'search']);

        return CommentResource::collection(
            $this->service->paginate(15, $filters)
        );
    }

    public function stats(): JsonResponse
    {
        return $this->success($this->service->stats());
    }

    /**
     * Action de modération générique
     * Gère : approved, rejected, spam, hidden
     */
    public function moderate(ModerateCommentRequest $request, Comment $comment): JsonResponse
    {
        try {
            $comment = $this->service->moderate(
                $comment,
                $request->validated('status'),
                $request->validated('rejection_reason')
            );

            return $this->success(
                new CommentResource($comment),
                'Commentaire modéré avec succès'
            );
        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }

    public function destroy(Comment $comment): JsonResponse
    {
        try {
            $this->service->delete($comment);

            return $this->success(message: 'Commentaire supprimé avec succès');
        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }

    /// ─ Public

    public function byPost(Post $post): JsonResponse
    {
        return $this->success(
            CommentResource::collection(
                $this->service->getApprovedByPost($post)
            )
        );
    }

    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        try {
            $comment = $this->service->create($post, $request->validated());

            return $this->success(
                new CommentResource($comment),
                'Commentaire soumis avec succès. Il sera visible après modération.',
                201
            );
        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }
}
