<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsletterSubscriberRequest;
use App\Http\Resources\NewsletterSubscriberResource;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterSubscriberService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;
use Throwable;

class NewsletterSubscriberController extends Controller
{
    use ApiResponseTrait;

    public function __construct( private readonly  NewsletterSubscriberService $service ) {}

    public function subscribe(NewsletterSubscriberRequest $request): JsonResponse
    {
        try {
            $subscriber = $this->service->subscribe($request->validated());

            return $this->success(
                new NewsletterSubscriberResource($subscriber),
                'Inscription enregistrée. Vérifiez votre boîte mail pour confirmer.',
                201
            );

        } catch (ValidationException $e) {
            return $this->error($e->errors()['email'][0], status: 422);

        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }

    /* public function confirm(string $token): JsonResponse
    {
        try {
            $subscriber = $this->service->confirm($token);

            return $this->success(
                new NewsletterSubscriberResource($subscriber),
                'Votre abonnement est confirmé. Merci !'
            );

        } catch (Throwable $e) {
            report($e);
            return $this->error($e->getMessage(), status: $e->getCode() ?: 400);
        }
    } */

    public function unsubscribe(string $token): JsonResponse
    {
        try {
            $this->service->unsubscribe($token);

            return $this->success(message: 'Vous avez été désabonné avec succès.');

        } catch (Throwable $e) {
            report($e);
            return $this->error($e->getMessage(), status: $e->getCode() ?: 400);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['status', 'search']);

        return NewsletterSubscriberResource::collection(
            $this->service->paginate(15, $filters)
        );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsletterSubscriber $subscriber): JsonResponse
    {
        try {
            $this->service->delete($subscriber);

            return $this->success(message: 'Abonné supprimé avec succès.');

        } catch (Throwable $e) {
            report($e);
            return $this->error('Une erreur est survenue.', status: 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }
}
