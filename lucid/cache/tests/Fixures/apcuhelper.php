<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Client;

function apcu_exists($key)
{
    if ('item.fails' === $key) {
        return false;
    }

    return true;
}

function apcu_store($key, $data, $ttl = 0)
{
    if ('item.fails' === $key) {
        return false;
    }

    return true;
}

function apcu_fetch($key, &$success)
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

function apcu_delete($key)
{
    if ('item.fails' === $key) {
        return false;
    }

    return true;
}

function apcu_inc($key, $value, &$success = null)
{
    if ('item.fails' === $key) {
        $success = false;
        return false;
    }

    $success = true;

    return 1 + $value;
}

function apcu_dec($key, $value, &$success = null)
{
    if ('item.fails' === $key) {
        $success = false;
        return false;
    }

    $success = true;

    return 1 - $value;
}

function apcu_clear_cache($type = null)
{
    return true;
}
