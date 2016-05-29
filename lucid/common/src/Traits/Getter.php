<?php

/*
 * This File is part of the Lucid\Common package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Traits;

/**
 * @trait Getter
 *
 * @package lucid/common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait Getter
{
    /**
     * Gets a value from a given associative array.
     *
     * If the key is not present in the input array, the default value will be returned.
     *
     * @param array  $pool The input array
     * @param string $attr the key to get from the input array
     * @param mixed  $default the default value
     *
     * @return mixed Result derived from `$pool`, otherwise `$default`.
     */
    protected function getDefault(array $pool, string $attr, $default = null)
    {
        return array_key_exists($attr, $pool) ? $pool[$attr] : $default;
    }

    /**
     * Gets a value from a given associative array.
     *
     * If the key is not present in the input array, a callback will be called to retreive the
     * default value.
     *
     * @param array    $pool The input array
     * @param string   $attr the key to get from the input array
     * @param \Closure $default the default getter
     *
     * @return mixed Result derived from `$pool`, otherwise results from `$default`.
     */
    protected function getDefaultUsing(array $pool, string $attr, callable $default)
    {
        return array_key_exists($attr, $pool) ? $pool[$attr] : $default();
    }

    /**
     * Gets a value from a given associative array.
     *
     * If the value is not set, the default value will be returned.
     *
     * @param array  $pool The input array
     * @param string $attr the key to get from the input array
     * @param mixed  $default the default value
     *
     * @return mixed Result derived from `$pool`, otherwise `$default`.
     */
    protected function getStrictDefault(array $pool, string $attr, $default = null)
    {
        return isset($pool[$attr]) ? $pool[$attr] : $default;
    }

    /**
     * Gets a value from a given associative array.
     *
     * If the value is not set, a callback will be called to retreive the
     * default value.
     *
     * @param array    $pool The input array
     * @param string   $attr the key to get from the input array
     * @param \Closure $default the default getter
     *
     * @return mixed Result derived from `$pool`, otherwise results from `$default`.
     */
    protected function getStrictDefaultUsing(array $pool, string $attr, callable $default)
    {
        return isset($pool[$attr]) ? $pool[$attr] : $default();
    }
}
