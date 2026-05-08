<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugService
{
    public function generateUnique(
        string $value,
        Model $model,
        string $column = 'slug',
        ?int $ignoreId = null
    ): string {
        $slug = $original = Str::slug($value);
        $count = 1;

        while (
            $model::where($column, $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }
}