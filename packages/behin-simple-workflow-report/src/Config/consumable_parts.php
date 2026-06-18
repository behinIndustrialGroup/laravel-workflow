<?php

return [
    'statuses' => [
        [
            'key' => 'requested',
            'label' => 'در انتظار تحویل انبار',
            'color' => '#f632468f',
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
            'color' => '#2bd144ff',
            'description' => '',
        ],
        [
            'key' => 'pending_buy_in',
            'label' => 'در انتظار خرید داخلی',
            'color' => '#f7e466ff',
            'description' => 'کالا در لیست خرید داخلی است',
        ],
        [
            'key' => 'pending_buy_out',
            'label' => 'در انتظار خرید خارجی',
            'color' => '#ffca1aff',
            'description' => 'کالا در لیست خرید خارجی است',
        ],
    ],
];
