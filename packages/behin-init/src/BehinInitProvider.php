<?php

namespace BehinInit;

use Illuminate\Support\ServiceProvider;

class BehinInitProvider extends ServiceProvider
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
        $this->publishes([
            __DIR__.'/resources/layouts' => resource_path('views/behin-layouts'),
            __DIR__.'/views' => resource_path('views'),
            __DIR__. '/public' => public_path('behin'),
            __DIR__. '/routes' => 'routes',
            __DIR__. '/config' => config_path(),
        ]);

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
}
