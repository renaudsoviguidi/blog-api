<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\SlugService;

class CategoryObserver
{
    //
    public function __construct(
        private readonly SlugService $slugService
    ) {}

    public function creating(Category $category): void
    {
        if (empty($category->slug)) {
            $category->slug = $this->slugService->generateUnique(
                $category->name,
                new Category()
            );
        }
    }

    public function updating(Category $category): void
    {
        if ($category->isDirty('name')) {
            $category->slug = $this->slugService->generateUnique(
                $category->name,
                new Category(),
                ignoreId: $category->id
            );
        }
    }
}
