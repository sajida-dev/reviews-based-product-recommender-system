<?php

namespace App\Providers;

use App\Services\QdrantHttpClient;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('qdrant.client', fn () => QdrantHttpClient::fromConfig());

        Inertia::share([
            'newReview' => fn () => session('newReview'),
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
