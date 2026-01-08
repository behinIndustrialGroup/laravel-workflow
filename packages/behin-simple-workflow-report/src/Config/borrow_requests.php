<?php

return [
    'statuses' => [
        'requested' => [
            'key' => 'requested',
            'label' => 'در انتظار تحویل',
            'description' => 'درخواست ثبت شده و منتظر تایید و تحویل انبار است.',
        ],
        'delivered' => [
            'key' => 'delivered',
            'label' => 'تحویل داده شد',
            'description' => 'کالا توسط انبار تحویل داده شده و نزد درخواست‌کننده است.',
        ],
        'pending_return_confirmation' => [
            'key' => 'pending_return_confirmation',
            'label' => 'در انتظار تایید بازگشت کالا به انبار',
            'description' => 'درخواست‌کننده بازگشت کالا را ثبت کرده و منتظر تایید انبار است.',
        ],
        'returned' => [
            'key' => 'returned',
            'label' => 'تحویل گرفته شد',
            'description' => 'کالا توسط انبار تحویل گرفته شده و درخواست بسته شده است.',
        ],
    ],
];
