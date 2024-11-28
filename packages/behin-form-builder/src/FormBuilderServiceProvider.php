<?php

namespace MyFormBuilder;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use MyFormBuilder\Facades\Form;
use MyFormBuilder\Services\FormBuilder;

class FormBuilderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $loader = AliasLoader::getInstance();

        // Add your aliases
        $loader->alias('Form', Form::class);
        $this->app->singleton('form', function ($app) {
            return new FormBuilder();
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'form-builder');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/form-builder'),
        ], 'form-builder-views');
    }
}
