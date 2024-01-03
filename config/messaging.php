<?php

return [

    'rabbitmq_creds' => [
        'host' => env('RABBITMQ_HOST', 'woodpecker.rmq.cloudamqp.co'),
        'gui' => env('RABBITMQ_GUI', ''),
        'port' => env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_USER', ''),
        'password' => env('RABBITMQ_PASSWORD', ''),
        'vhost' => env('RABBITMQ_VHOST', ''),
        'queue_suffix' => env('RABBITMQ_QUEUE_SUFFIX', ''),
    ],

    'sync_apikey' => env('SYNC_APIKEY', ''),
];
