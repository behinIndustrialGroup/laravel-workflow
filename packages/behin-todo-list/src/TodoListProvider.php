<?php

namespace TodoList;

use Illuminate\Support\ServiceProvider;

class TodoListProvider extends ServiceProvider
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
            __DIR__ . '/public' => public_path('packages/behin-todo-list/')
        ]);
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadViewsFrom(__DIR__. '/views', 'TodoListViews');
        $this->loadJsonTranslationsFrom(__DIR__. '/Lang');
    }
}
