<?php

return [
    'amqp' => [
        'zt' => [
            'host' => env('GROW_SYSTEM_CENTER_MQ_HOST'),
            'port' => env('GROW_SYSTEM_CENTER_MQ_PORT'),
            'login' => env('GROW_SYSTEM_CENTER_MQ_USERNAME'),
            'password' => env('GROW_SYSTEM_CENTER_MQ_PASSWORD'),
            'vhost' => 'userlevel',
            'read_timeout' => 10,
            'write_timeout' => 1,
            'connect_timeout' => 1,
            'heartbeat' => 10,
        ],
        'proverb' => [
            'host' => env('PROVERB_MQ_HOST'),
            'port' => env('PROVERB_MQ_PORT'),
            'login' => env('PROVERB_MQ_USERNAME'),
            'password' => env('PROVERB_MQ_PASSWORD'),
            'vhost' => 'chy',
            'read_timeout' => 10,
            'write_timeout' => 1,
            'connect_timeout' => 1,
            'heartbeat' => 10,
        ],
    ],
];
