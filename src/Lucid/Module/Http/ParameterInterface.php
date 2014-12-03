<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http;

/**
 * @class ParameterInterface
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ParameterInterface
{
    /**
     * Checks if collection has this key set
     *
     * @param string $key
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Gets an item by key
     *
     * Returns the default value if the key isn't set.
     *
     * @param string $key
     * @param mixed $default
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
     * Gets all item keys as array.
     *
     * @return array
     */
    public function keys();
}
