<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Observers\CategoryObserver;
use App\Observers\PostObserver;
use App\Observers\TagObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Passport::enablePasswordGrant();
        //Personnaliser le nom du cookie
        Passport::cookie('blog-api');

        // Utiliser le lifetime de session comme durée d'expiration des tokens
        Passport::tokensExpireIn(now()->addMinutes((int) config('session.lifetime')));
        Passport::refreshTokensExpireIn(now()->addDays(1));
        Passport::personalAccessTokensExpireIn(now()->addMinutes((int) config('session.lifetime') ?? 120)); //2heures

        Category::observe(CategoryObserver::class);
        Tag::observe(TagObserver::class);
        Post::observe(PostObserver::class);

        Schema::defaultStringLength(191);
    }
}
