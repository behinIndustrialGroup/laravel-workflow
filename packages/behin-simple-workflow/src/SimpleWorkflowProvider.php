<?php

namespace Behin\SimpleWorkflow;

use Illuminate\Support\ServiceProvider;

class SimpleWorkflowProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(__DIR__. '/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadViewsFrom(__DIR__. '/Views', 'SimpleWorkflowView');
        $this->loadTranslationsFrom('/lang', 'SimpleWorkflowLang');
    }
}
