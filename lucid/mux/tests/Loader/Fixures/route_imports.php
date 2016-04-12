<?php

use Lucid\Mux\RouteCollectionBuilder;

return [
    'index' => [
        'pattern' => '/',
        'method' => 'GET',
        'handler' => 'indexAction'
    ],
    'admin' => [
        [
            ['resources' => ['imports.0.routes']]
        ]
    ]
];
