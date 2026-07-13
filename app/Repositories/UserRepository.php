<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->when(
                isset($filters['search']),
                fn ($q) => $q->where('name', 'like', '%' . $filters['search'] . '%')
                            ->orWhere('email', 'like', '%' . $filters['search'] . '%')
            )
            ->when(
                isset($filters['role']),
                fn ($q) => $q->whereHas('roles',
                    fn ($q) => $q->where('id', $filters['role'])
                )
            )
            ->when(
                isset($filters['is_active']),
                fn ($q) => $q->where('is_active', $filters['is_active'])
            )
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user->fresh('roles');
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function countByStatus(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
        ];
    }
}