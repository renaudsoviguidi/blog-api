<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Pagination\LengthAwarePaginator;

class TagRepository
{
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return Tag::latest('id')->paginate($perPage);
    }

    public function create(array $data): Tag
    {
        return Tag::create($data);
    }

    public function update(Tag $tag, array $data): Tag
    {
        $tag->update($data);
        return $tag->fresh();
    }

    public function delete(Tag $tag): void
    {
        $tag->delete();
    }
}