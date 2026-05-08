<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly CategoryService $service) {}
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        //
        return CategoryResource::collection(
            $this->service->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        //
        try {
            $category = $this->service->create(
                $request->validated()
            );

            return $this->success(
                new CategoryResource($category),
                'Catégorie ajoutée avec succès',
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
    public function show(Category $category): JsonResponse
    {
        //
        return $this->success(new CategoryResource($category));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        //
        try {
            $category = $this->service->update(
                $category,
                $request->validated()
            );

            return $this->success(
                new CategoryResource($category),
                'Catégorie mise à jour avec succès'
            );

        } catch (Throwable $e) {
            report($e);

            return $this->error('Une erreur est survenue lors de la mise à jour.', status: 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        //
        try {
            $this->service->delete($category);

            return $this->success(message: 'Catégorie supprimée avec succès');

        } catch (Throwable $e) {
            report($e);

            return $this->error('Une erreur est survenue lors de la suppression.', status: 500);
        }
    }
}
