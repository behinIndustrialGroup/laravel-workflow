<?php

return [
    'statuses' => [
        'requested' => [
            'key' => 'requested',
            'label' => 'در انتظار تحویل',
            'color' => '#f63245ff',
            'description' => 'درخواست ثبت شده و منتظر تایید و تحویل انبار است.',
        ],
        'delivered' => [
            'key' => 'delivered',
            'label' => 'تحویل داده شد',
            'color' => '#efa613ff',
            'description' => 'کالا توسط انبار تحویل داده شده و نزد درخواست‌کننده است.',
        ],
        'pending_return_confirmation' => [
            'key' => 'pending_return_confirmation',
            'label' => 'در انتظار تایید بازگشت کالا به انبار',
            'color' => '#e6fb93ff',
            'description' => 'درخواست‌کننده بازگشت کالا را ثبت کرده و منتظر تایید انبار است.',
        ],
        'returned' => [
            'key' => 'returned',
            'label' => 'تحویل گرفته شد',
            'color' => '#00b506ff',
            'description' => 'کالا توسط انبار تحویل گرفته شده و درخواست بسته شده است.',
        ],
    ],
];
