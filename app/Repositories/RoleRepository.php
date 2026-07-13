<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository
{
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        return Role::query()
            ->withCount('users')
            ->with('habilitations')
            ->when(
                isset($filters['search']),
                fn ($q) => $q->where('libelle', 'like', '%' . $filters['search'] . '%')
            )
            ->latest()
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return Role::query()
            ->withCount('users')
            ->with('habilitations')
            ->get();
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(Role $role, array $data): Role
    {
        $role->update($data);
        return $role->fresh(['habilitations']);
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}