<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Services\TagService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class TagController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly TagService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        //
        return TagResource::collection(
            $this->service->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request)
    {
        //
        try {
            $tag = $this->service->create(
                $request->validated()
            );

            return $this->success(
                new TagResource($tag),
                'Tag ajouté avec succès',
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
    public function show(Tag $tag): JsonResponse
    {
        //
        return $this->success(new TagResource($tag));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        //
        try {
            $tag = $this->service->update(
                $tag,
                $request->validated()
            );

            return $this->success(
                new TagResource($tag),
                'Tag mis à jour avec succès'
            );

        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue lors de la mise à jour.', status: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): JsonResponse
    {
        //
        try {
            $this->service->delete($tag);
            return $this->success(message: 'Tag supprimé avec succès');

        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue lors de la suppression.', status: 500);
        }
    }
}
