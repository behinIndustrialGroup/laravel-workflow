<?php 

return [
    'max_multiple_file_upload' => 10,
    'debug' => env('PM_DEBUG', false),
    'access_token_exp_in_minute' => 10,
    'processes' => [
        'vacation' => [
            'triggers' => [
                'set user id' => '68379534364e3501fd57709063851566',
                'set name of requester' => '98954906564e3553a082989062656447'
            ]
        ]
    ],
];