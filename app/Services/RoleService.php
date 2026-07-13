<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleService
{
    public function __construct(private readonly RoleRepository $repository) {}

    public function paginate(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $habilitationIds = $data['habilitation_ids'] ?? [];

            $role = $this->repository->create([
                'libelle' => strtoupper($data['libelle']),
                'description' => $data['description'] ?? null,
                'ref' => Str::uuid(),
            ]);

            /// - Enregistrer les habilitations dans la base
            if ($habilitationIds) {
                $role->habilitations()->sync($habilitationIds);
            }

            return $role->load('habilitations');
        });
    }

    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $habilitationIds = $data['habilitation_ids'] ?? [];

            $role = $this->repository->update($role, [
                'libelle' => strtoupper($data['libelle']),
                'description' => $data['description'] ?? null
            ]);

            /// - Update les habilitations s'il y a un changement
            $role->habilitations()->sync($habilitationIds);

            return $role->load('habilitations');
        });
    }

    public function delete(Role $role): void
    {
        DB::transaction(function () use ($role) {
            /// - Vérifier qu'aucun utilisateur n'a ce rôle
            if ($role->users()->count() > 0) {
                throw new \Exception(
                    "Impossible de supprimer ce rôle : {$role->users()->count()} utilisateur(s) l'utilisent encore."
                );
            }
            $role->habilitations()->detach();
            $this->repository->delete($role);
        });
    }
}