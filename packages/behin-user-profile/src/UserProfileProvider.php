<?php

namespace UserProfile;

use Illuminate\Support\ServiceProvider;

class UserProfileProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__. '/views', 'UserProfileViews');
        $this->loadJsonTranslationsFrom(__DIR__. '/Lang');
    }
}
