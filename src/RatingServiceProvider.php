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

        $this->publishes([
            __DIR__.'/../database/migrations/2018_07_14_183253_ratings.php' => database_path('migrations/2018_07_14_183253_ratings.php'),
        ], 'migration');
    }
}
