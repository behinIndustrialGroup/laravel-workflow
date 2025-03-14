<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\BroadcastServiceProvider::class,
    Barryvdh\TranslationManager\ManagerServiceProvider::class,
    BehinFileControl\BehinFileControlProvider::class,
    BehinInit\BehinInitProvider::class,
    BehinLogging\ServiceProvider::class,
    BehinProcessMakerAdmin\BehinProcessMakerAdminProvider::class,
    BehinProcessMaker\BehinProcessMakerProvider::class,
    BehinUserRoles\UserRolesServiceProvider::class,
    Behin\PMCaseNumbering\PackageServiceProvider::class,
    Behin\SimpleWorkflowReport\SimpleWorkflowReportProvider::class,
    Behin\SimpleWorkflow\SimpleWorkflowProvider::class,
    Behin\Sms\SmsProvider::class,
    FileService\FileServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,
    Mkhodroo\Cities\CityProvider::class,
    MyFormBuilder\FormBuilderServiceProvider::class,
    TodoList\TodoListProvider::class,
    UserProfile\UserProfileProvider::class,
];
