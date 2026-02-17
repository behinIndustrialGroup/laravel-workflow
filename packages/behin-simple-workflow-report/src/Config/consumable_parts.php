<?php

return [
    'statuses' => [
        [
            'key' => 'requested',
            'label' => 'در انتظار تحویل انبار',
            'color' => '#f63245ff',
            'description' => 'درخواست ثبت شده و منتظر تایید و تحویل انبار است.',
        ],
        [
            'key' => 'returned',
            'label' => 'در انتظار تایید بازگشتی ها توسط انبار',
            'color' => '#f63245ff',
            'description' => 'تعدادی از کالا ها بازگشت داده شده به انبار است و منتظر تایید انبار است',
        ],
        [
            'key' => 'confirmed',
            'label' => 'تایید',
            'color' => '#efa613ff',
            'description' => '',
        ],
        [
            'key' => 'pending_buy_in',
            'label' => 'در انتظار خرید داخلی',
            'color' => '#e6fb93ff',
            'description' => 'کالا در لیست خرید داخلی است',
        ],
        [
            'key' => 'pending_buy_out',
            'label' => 'در انتظار خرید خارجی',
            'color' => '#00b506ff',
            'description' => 'کالا در لیست خرید خارجی است',
        ],
    ],
];
