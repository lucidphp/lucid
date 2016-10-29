<?php declare(strict_types=1);

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
