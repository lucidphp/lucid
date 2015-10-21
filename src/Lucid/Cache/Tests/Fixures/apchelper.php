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

function apc_exists($key)
{
    if ('item.fails' === $key) {
        return false;
    }

    return true;
}

function apc_store($key, $data, $ttl = 0)
{
    if ('item.fails' === $key) {
        return false;
    }

    return true;
}

function apc_fetch($key, &$success)
{
    if ('item.fails' === $key) {
        $success = false;
        return false;
    }

    $success = true;

    if ('item.exists' === $key) {
        return 'exists';
    }

    return 'item';
}

function apc_delete($key)
{
    if ('item.fails' === $key) {
        return false;
    }

    return true;
}

function apc_inc($key, $value, &$success = null)
{
    if ('item.fails' === $key) {
        $success = false;
        return false;
    }

    $success = true;

    return 1 + $value;
}

function apc_dec($key, $value, &$success = null)
{
    if ('item.fails' === $key) {
        $success = false;
        return false;
    }

    $success = true;

    return 1 - $value;
}

function apc_clear_cache($type = null)
{
    return true;
}
