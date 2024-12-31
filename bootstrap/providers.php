<?php

use Behin\PMCaseNumbering\PackageServiceProvider;
use Behin\SimpleWorkflow\SimpleWorkflowProvider;
use Behin\SimpleWorkflowReport\SimpleWorkflowReportProvider;
use Behin\Sms\SmsProvider;
use BehinFileControl\BehinFileControlProvider;
use BehinInit\BehinInitProvider;
use BehinLogging\ServiceProvider;
use BehinProcessMaker\BehinProcessMakerProvider;
use BehinProcessMakerAdmin\BehinProcessMakerAdminProvider;
use BehinUserRoles\UserRolesServiceProvider;
use FileService\FileServiceProvider;
use Mkhodroo\Cities\CityProvider;
use MyFormBuilder\FormBuilderServiceProvider;
use TodoList\TodoListProvider;
use UserProfile\UserProfileProvider;

return [
    App\Providers\AppServiceProvider::class,
    BehinInitProvider::class,
    UserRolesServiceProvider::class,
    UserProfileProvider::class,
    BehinProcessMakerProvider::class,
    BehinFileControlProvider::class,
    ServiceProvider::class,
    BehinProcessMakerAdminProvider::class,
    SmsProvider::class,
    PackageServiceProvider::class,
    TodoListProvider::class,
    FileServiceProvider::class,
    SimpleWorkflowProvider::class,
    FormBuilderServiceProvider::class,
    CityProvider::class,
    SimpleWorkflowReportProvider::class
];
