<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'alumni',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'alumni',
        ],
    ],

    'providers' => [
        'alumni' => [
            'driver' => 'eloquent',
            'model' => App\Models\Alumni::class,
        ],
    ],

    'passwords' => [
        'alumni' => [
            'provider' => 'alumni',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
