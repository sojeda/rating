<?php

namespace Laraveles\Rating;

use Illuminate\Support\ServiceProvider;

class RatingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/rating.php' => config_path('rating.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');

        $this->loadTranslationsFrom(__DIR__.'/../lang/', 'rating');

        $this->publishes([
            __DIR__.'/../lang/' => resource_path('lang/vendor/rating'),
        ]);
    }
}
