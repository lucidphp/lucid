<?php

use Lucid\Mux\RouteCollectionBuilder;

return [
    'index' => [
        'pattern' => '/',
        'method' => 'GET',
        'handler' => 'indexAction'
    ],
    'backstage' => [
        'requirements' => [
            RouteCollectionBuilder::K_SCHEME => 'https',
            RouteCollectionBuilder::K_HOST => 'example.com'
        ],
        [
            'users' => [
                'pattern' => '/user/{id}',
                'method' => 'GET',
                'handler' => 'userAction'
            ],

            'affiliates' => [
                'pattern' => '/affiliates/{id}',
                'method' => 'GET',
                'handler' => 'afltAction'
            ],
        ]
    ]
];
