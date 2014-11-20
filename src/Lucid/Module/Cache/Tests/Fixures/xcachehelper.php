<?php

/*
 * This File is part of the Lucid\Module\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Cache\Driver;

function xcache_isset($key)
{
    if ('item.fails' === $key) {
        return false;
    }

    return true;
}

function xcache_get($key)
{
    if ('item.fails' === $key) {
        return false;
    }

    if ('item.exists' === $key) {
        return serialize('exists');
    }

    return serialize('item');
}

function xcache_set($key, $value)
{
    if ('item.fails' === $key) {
        return false;
    }

    return true;
}

function xcache_unset($key)
{
    if ('item.fails' === $key) {
        return false;
    }

    return true;
}

function xcache_inc($key, $value)
{
    if ('item.fails' === $key) {
        return false;
    }

    return 1 + $value;
}

function xcache_dec($key, $value)
{
    if ('item.fails' === $key) {
        return false;
    }

    return 1 - $value;
}

function xcache_clear_cache()
{
    return true;
}
