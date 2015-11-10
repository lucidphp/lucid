<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http;

/**
 * @class ParameterInterface
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ParameterInterface
{
    /**
     * Checks if a key is present in the parameter set.
     *
     * @param string $key the parameter key.
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Gets an item by key
     *
     * Returns the default value if the key isn't set.
     *
     * @param string $key the parameter key
     * @param mixed $default the default value to return if no item is present
     * at $key
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Gets all items as array.
     *
     * @return array
     */
    public function all();

    /**
     * Extracts all item keys as array.
     *
     * @return array
     */
    public function keys();
}
