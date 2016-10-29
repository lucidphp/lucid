<?php declare(strict_types=1);

return [
    'index' => [
        'pattern' => '/',
        'method' => 'GET',
        'handler' => 'indexAction'
    ],
    'users' => [
        'pattern' => '/user/{id}',
        'method' => 'GET',
        'handler' => 'userAction'
    ],
];
