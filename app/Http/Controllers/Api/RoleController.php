<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\HabilitationResource;
use App\Http\Resources\RoleResource;
use App\Models\Habilitation;
use App\Models\Role;
use App\Services\RoleService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class RoleController extends Controller
{

    use ApiResponseTrait;

    public function __construct(private readonly RoleService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        //
        $filters = $request->only(['search']);

        return RoleResource::collection(
            $this->service->paginate(10, $filters)
        );
    }

    /// - Retourner tous les rôles sans pagination (pour les selects)
    public function all(): JsonResponse
    {
        return $this->success(
            RoleResource::collection($this->service->all())
        );
    }

    /// - Toutes les habilitations disponibles (pour le formulaire)
    public function habilitations(): JsonResponse
    {
        $habilitations = Habilitation::orderBy('slug')->get();

        return $this->success(
            HabilitationResource::collection($habilitations)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        //
        try {
            $role = $this->service->create($request->validated());

            return $this->success(
                new RoleResource($role),
                'Rôle créé avec succès',
                201
            );
        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.' . $e->getMessage(), status: 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        try {
            $role = $this->service->update($role, $request->validated());

            return $this->success(
                new RoleResource($role),
                'Rôle mis à jour avec succès'
            );
        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }

    public function destroy(Role $role): JsonResponse
    {
        try {
            $this->service->delete($role);

            return $this->success(message: 'Rôle supprimé avec succès');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 422);
        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }
}
