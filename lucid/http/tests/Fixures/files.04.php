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
 * expected result:
 * ```php
 * array(
 *     'my-form' => array(
 *         'details' => array(
 *             'avatar' => ...
 *         ),
 *     ),
 * )
 *
 * @return array ```
 */

return [
    'my-form' => [
        'details' => [
            'avatars' => [
                'name' => [
                    'my-avatar.png',
                    'my-avatar.jpg'
                ],
                'tmp_name' => [
                    'phpUxcOty',
                    'phpjUziAf'
                ],
                'size' => [
                    90006,
                    80566
                ],
                'type' => [
                    'image/png',
                    'image/jpeg'
                ],
                'error' => [
                    UPLOAD_ERR_OK,
                    UPLOAD_ERR_OK
                ],
            ],
        ],
    ],
];
