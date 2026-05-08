<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\TagRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TagService
{
    public function __construct(
        private readonly TagRepository $repository
    ) {}

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function create(array $data): Tag
    {
        return DB::transaction(fn () => $this->repository->create($data));
    }

    public function update(Tag $tag, array $data): Tag
    {
        return DB::transaction(fn () => $this->repository->update($tag, $data));
    }

    public function delete(Tag $tag): void
    {
        DB::transaction(fn () => $this->repository->delete($tag));
    }
}