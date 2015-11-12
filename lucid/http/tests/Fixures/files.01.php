<?php

/*
 * This File is part of the lucid/http-infuse package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

/**
 * @example
 * expected structore
 *
 * ```php
 * array(
 *     'files' => array(
 *         0 => array(
 *             'name' => 'file0.txt',
 *             'type' => 'text/plain',
 *              //etc ...
 *         ),
 *         1 => array(
 *             'name' => 'file1.html',
 *             'type' => 'text/html',
 *              //etc ...
 *         ),
 *     ),
 * )
 * ```
 * @return array
 */
return [
    'files' => [
        'name' => [
            'file0.txt',
            'file1.html',
        ],
        'tmp_name' => [
            '/var/tmp/gal9548kjhs',
            '/var/tmp/0z5473euuLk',
        ],
        'type' => [
            'text/plain',
            'text/html',
        ],
        'size' => [
            104,
            92,
        ],
        'error' => [
            UPLOAD_ERR_OK,
            UPLOAD_ERR_OK
        ],
    ],
];
