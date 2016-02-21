<?php

use Lucid\Mux\RouteCollectionBuilder;

return [
    'index' => [
        'pattern' => '/',
        'method' => 'GET',
        'handler' => 'indexAction'
    ],
    'backstage' => [
        'pattern' => '/admin/area',
        [
            'users' => [
                'pattern' => '/user/{id}',
                'method' => 'GET',
                'handler' => 'userAction'
            ]
        ]
    ]
];
