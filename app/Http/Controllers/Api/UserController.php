<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;


class UserController extends Controller
{
    //

    use ApiResponseTrait;

    public function __construct(private readonly UserService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['search', 'role', 'is_active']);

        return UserResource::collection(
            $this->service->paginate(15, $filters)
        );
    }

    public function stats(): JsonResponse
    {
        return $this->success($this->service->stats());
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(
            new UserResource($user->load('roles'))
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = $this->service->create($request->validated());

            return $this->success(
                new UserResource($user),
                'Utilisateur créé avec succès',
                201
            );
        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        try {
            $user = $this->service->update($user, $request->validated());

            return $this->success(
                new UserResource($user),
                'Utilisateur mis à jour avec succès'
            );
        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }

    public function toggleActive(User $user): JsonResponse
    {
        try {
            $user = $this->service->toggleActive($user);

            return $this->success(
                new UserResource($user),
                $user->is_active
                    ? 'Utilisateur activé avec succès'
                    : 'Utilisateur désactivé avec succès'
            );
        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            // Empêcher la suppression de son propre compte
            if ($user->id === auth()->id()) {
                return $this->error(
                    'Vous ne pouvez pas supprimer votre propre compte.',
                    status: 403
                );
            }

            $this->service->delete($user);

            return $this->success(message: 'Utilisateur supprimé avec succès');
        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }
}
