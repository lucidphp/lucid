<?php

/*
 * This File is part of the Lucid\Http\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Request;

/**
 * @class Method
 *
 * @package Lucid\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Method
{
    private static $map = [
        'GET'    => 'GET',
        'HEAD'   => 'GET',
        'POST'   => 'POST',
        'PUT'    => 'PUT',
        'PATCH'  => 'PUT',
        'DELETE' => 'DELETE',
    ];

    public static function map($method)
    {
        $m = strtoupper($method);

        if (array_key_exists(static::$map[$m])) {
            return static::$map[$m];
        }

        return null;
    }
}
