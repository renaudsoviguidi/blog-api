<?php

namespace App\Repositories;

use App\Models\NewsletterSubscriber;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NewsletterSubscriberRepository
{
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        return NewsletterSubscriber::query()
            ->when(
                isset($filters['status']),
                fn ($q) => $q->where('is_active', $filters['status'])
            )
            ->when(
                isset($filters['search']),
                fn ($q) => $q->where('email', 'like', '%' . $filters['search'] . '%')
                //->orWhere('email', 'like', '%' . $filters['search'] . '%')
            )
            ->latest()
            ->paginate($perPage);
    }

    public function findByEmail(string $email): ?NewsletterSubscriber
    {
        return NewsletterSubscriber::where('email', $email)->first();
    }

    public function findByToken(string $token): ?NewsletterSubscriber
    {
        return NewsletterSubscriber::where('unsubscribe_token', $token)->first();
    }

    public function create(array $data): NewsletterSubscriber
    {
        return NewsletterSubscriber::create($data);
    }

    public function delete(NewsletterSubscriber $subscriber): void
    {
        $subscriber->delete();
    }

    public function allConfirmedEmails(): Collection
    {
        return NewsletterSubscriber::confirmed()->pluck('email');
    }
}