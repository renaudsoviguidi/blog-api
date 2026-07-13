<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejectPostRequest;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class PostController extends Controller
{
    use ApiResponseTrait;
    public function __construct( private readonly PostService $service ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        //
        $filters = $request->only(['status', 'search', 'category_ref', 'tag_ref']);

        $user = auth('api')->user();
        if (!$user->hasRole('ADMIN')) {
            $filters['user_id'] = $user->id;
        }

        return PostResource::collection(
            $this->service->paginate(10, $filters)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        //
        try {
            $post = $this->service->create($request->validated());

            return $this->success(
                new PostResource($post),
                'Post créé avec succès',
                201
            );

        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue lors de la création.', status: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        //
        return $this->success(
            new PostResource($this->service->show($post, trackView: true))
        );
    }

    public function edit(Post $post): JsonResponse
    {
        return $this->success(
            new PostResource($this->service->show($post, trackView: false))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        //
        $this->authorize('update', $post);
        try {
            $post = $this->service->update($post, $request->validated());

            return $this->success(
                new PostResource($post),
                'Post mis à jour avec succès'
            );

        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue lors de la mise à jour.', status: 500);
        }
    }

    public function publish(Post $post): JsonResponse
    {
        try {
            $post = $this->service->publish($post);

            return $this->success(
                new PostResource($post),
                'Post publié avec succès'
            );

        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue lors de la publication.', status: 500);
        }
    }

    public function reject(RejectPostRequest $request, Post $post): JsonResponse
    {
        try {
            $post = $this->service->reject(
                $post,
                $request->validated()['reason']
            );

            return $this->success(
                new PostResource($post),
                'Post rejeté avec succès'
            );

        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue lors du rejet.', status: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        //
        try {
            $this->service->delete($post);

            return $this->success(message: 'Post supprimé avec succès');

        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue lors de la suppression.', status: 500);
        }
    }

    /**
     * Articles les plus vus (tendances)
     */
    public function trending(): JsonResponse
    {
        $posts = Post::query()
            ->with(['author', 'categories'])
            ->published()
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();

        return $this->success(PostResource::collection($posts));
    }

    /**
     * Articles recommandés — les plus récents par catégorie
     */
    public function recommended(): JsonResponse
    {
        $posts = Post::query()
            ->with(['author', 'categories'])
            ->published()
            ->latest('published_at')
            ->limit(4)
            ->get();

        return $this->success(PostResource::collection($posts));
    }
}
