<?php

namespace BehinProcessMaker;

use Illuminate\Support\ServiceProvider;

class BehinProcessMakerProvider extends ServiceProvider
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
        $this->publishes(
            [ 
                __DIR__.'/config.php' => config_path('pm_config.php')
            ]
        );
        $this->loadMigrationsFrom(__DIR__. '/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__. '/Views', 'PMViews');
        $this->loadJsonTranslationsFrom(__DIR__.'/Lang/fa/');
    }
}
