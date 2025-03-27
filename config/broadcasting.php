<?php

return [
    'default' => env('BROADCAST_DRIVER', 'pusher'),
    'pusher' => [
        'driver' => 'pusher',
        'instanceId' => env('PUSHER_INSTANCE_ID'),
        'secretKey' => env('PUSHER_SECRET_KEY'),
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true,
            'host' => 'localhost',
            'port' => 6001,
            'scheme' => 'http'
        ],
        'prefix_user' => 'user-mobile-'
    ],
];
