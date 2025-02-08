<?php

return [
    'menu' =>[

        'dashboard' => [
            'icon' => 'fa fa-dashboard',
            'fa_name' => 'داشبرد',
            'submenu' => [
                'dashboard' => [ 'fa_name' => 'داشبرد', 'route-name' => '', 'route-url' => 'admin' ],
            ]
        ],
        'cases' => [
            'icon' => 'fa fa-list',
            'fa_name' => 'کارپوشه',
            'submenu' => [
                'new-case' => [ 'fa_name' => 'فرایند جدید', 'route-name' => 'MkhodrooProcessMaker.forms.start', 'route-url' => '' ],
                'inbox' => [ 'fa_name' => 'انجام نشده ها', 'route-name' => 'MkhodrooProcessMaker.forms.todo', 'route-url' => '' ],
                'done' => [ 'fa_name' => 'انجام شده ها', 'route-name' => 'MkhodrooProcessMaker.forms.done', 'route-url' => '' ],
                'draft' => [ 'fa_name' => 'پیش نویس', 'route-name' => 'MkhodrooProcessMaker.forms.draft', 'route-url' => '' ]
            ]
        ],
        'workflow-inbox' => [
            'icon' => '',
            'fa_name' => 'کارتابل',
            'submenu' => [
                'new-process' => [ 'fa_name' => 'فرایند جدید', 'route-name' => 'simpleWorkflow.process.startListView' ],
                'inbox' => [ 'fa_name' => 'کارتابل', 'route-name' => 'simpleWorkflow.inbox.index' ],
                'done-inbox' => [ 'fa_name' => 'انجام شده ها', 'route-name' => 'simpleWorkflow.inbox.done' ],
            ]
        ],
        'workflow-report' => [
            'icon' => '',
            'fa_name' => 'گزارشات کارتابل',
            'submenu' => [
                'list' => [ 'fa_name' => 'لیست', 'route-name' => 'simpleWorkflowReport.index' ],
                'summary' => [ 'fa_name' => 'خلاصه', 'route-name' => 'simpleWorkflowReport.summary-report.index' ],
                'role-form-control' => [ 'fa_name' => 'فرم گزارش نقش ها', 'route-name' => 'simpleWorkflowReport.role.index' ],
            ]
        ],
        'workflow' => [
            'icon' => '',
            'fa_name' => 'گردش کار',
            'submenu' => [
                'process' => [ 'fa_name' => 'فرایند', 'route-name' => 'simpleWorkflow.process.index' ],
                'task-actors' => [ 'fa_name' => 'تسک ها', 'route-name' => 'simpleWorkflow.task-actors.index' ],
                'forms' => [ 'fa_name' => 'فرم ها', 'route-name' => 'simpleWorkflow.form.index'  ],
                'scripts' => [ 'fa_name' => 'اسکریپت ها', 'route-name' => 'simpleWorkflow.scripts.index' ],
                'conditions' => [ 'fa_name' => 'شرط ها', 'route-name' => 'simpleWorkflow.conditions.index' ],
                'fields' => [ 'fa_name' => 'فیلدها', 'route-name' => 'simpleWorkflow.fields.index' ],
                'entities' => [ 'fa_name' => 'موجودیت ها', 'route-name' => 'simpleWorkflow.entities.index' ],
                'all-inbox' => [ 'fa_name' => 'کارتابل همه', 'route-name' => 'simpleWorkflow.inbox.cases.list' ],
            ]
        ],
        'translations' => [
            'icon' => 'fa fa-language',
            'fa_name' => 'ترجمه',
            'submenu' => [
                'index' => [ 'fa_name' => 'ترجمه', 'route-name' => '', 'route-url' => '/translations' ],
            ]
        ],
        'cases-report' => [
            'icon' => 'fa fa-list',
            'fa_name' => 'گزارشات کارپوشه',
            'submenu' => [
                'all' => [ 'fa_name' => 'همه', 'route-name' => 'pmAdmin.form.allCasesForm', 'route-url' => '' ],
                'filter-by-last-status' => [ 'fa_name' => 'دسته بندی', 'route-name' => 'pmAdmin.form.casesByLastStatusView', 'route-url' => '' ],
                'fin-report' => [ 'fa_name' => 'مالی', 'route-name' => 'pmAdmin.form.finListView', 'route-url' => '' ],
            ]
        ],
        'users' => [
            'icon' => 'fa fa-user',
            'fa_name' => 'کاربران',
            'submenu' => [
                'dashboard' => [ 'fa_name' => 'همه', 'route-name' => '', 'route-url' => 'user/all' ],
                'role' => [ 'fa_name' => 'نقش ها', 'route-name' => 'role.listForm', 'route-url' => '' ],
                'method' => [ 'fa_name' => 'متد ها', 'route-name' => 'method.list', 'route-url' => '' ],
                'department' => [ 'fa_name' => 'دپارتمان ها', 'route-name' => 'department.index', 'route-url' => '' ],
            ]
        ],
        'tickets' => [
            'icon' => 'fa fa-ticket',
            'fa_name' => 'تیکت پشتیبانی',
            'submenu' => [
                'create' => [ 'fa_name' => 'ایجاد', 'route-name' => 'ATRoutes.index', 'route-url' => '' ],
                'show' => [ 'fa_name' => 'مشاهده', 'route-name' => 'ATRoutes.show.listForm', 'route-url' => '' ],
            ]
        ],

    ]
];
