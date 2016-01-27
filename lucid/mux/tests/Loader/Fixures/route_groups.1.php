<?php

use Lucid\Mux\RouteCollectionBuilder;

return [
    'index' => [
        'pattern' => '/',
        'method' => 'GET',
        'handler' => 'indexAction'
    ],
    'backstage' => [
        [
            'users' => [
                'pattern' => '/user/{id}',
                'method' => 'GET',
                'handler' => 'userAction'
            ]
        ]
    ]
];
