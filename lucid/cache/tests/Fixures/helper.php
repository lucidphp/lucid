<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Driver;

function extension_loaded($ext)
{
    if (in_array($ext, ['apc', 'apcu', 'xcache'])) {
        return true;
    }

    return call_user_func_array('extension_loaded', func_get_args());
}
