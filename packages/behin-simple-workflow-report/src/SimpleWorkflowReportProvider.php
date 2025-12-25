<?php

namespace Behin\SimpleWorkflowReport;

use Illuminate\Support\ServiceProvider;

class SimpleWorkflowReportProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/borrow_requests.php', 'simpleWorkflowReport.borrow_requests');
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
        $this->loadViewsFrom(__DIR__. '/Views', 'SimpleWorkflowReportView');
        $this->loadTranslationsFrom(__DIR__ . '/Lang', 'SimpleWorkflowReportLang');

        $this->publishes([
            __DIR__ . '/Config/borrow_requests.php' => config_path('simpleWorkflowReport/borrow_requests.php'),
        ], 'simple-workflow-report-config');
    }
}
