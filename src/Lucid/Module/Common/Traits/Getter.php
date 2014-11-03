<?php

/*
 * This File is part of the Lucid\Module\Common\Traits package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Common\Traits;

/**
 * @class Getter
 *
 * @package Lucid\Module\Common\Traits
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait Getter
{
    /**
     * Gets a value from a given array.
     *
     * If the value is not present, the default value will be returned.
     *
     * @param array $pool
     * @param string $attr
     * @param mixed $default
     *
     * @return mixed
     */
    protected function getDefault(array $pool, $attr, $default = null)
    {
        return array_key_exists($attr, $pool) ? $pool[$attr] : $default;
    }

    /**
     * Gets a value from a given array.
     *
     * If the value is not present, a callback will be called to retreive the
     * defatul value
     *
     * @param array $pool
     * @param string $attr
     * @param \Closure $default
     *
     * @return mixed
     */
    protected function getDefaultUsing(array $pool, $attr, \Closure $default)
    {
        return array_key_exists($attr, $pool) ? $pool[$attr] : $default();
    }
}
