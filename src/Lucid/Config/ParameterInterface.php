<?php

/*
 * This File is part of the Lucid\Config package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Config;

/**
 * @class ParameterInterface
 * @see \ArrayAccess
 *
 * @package Lucid\Config
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ParameterInterface extends \ArrayAccess
{
    /**
     * Sets a parameter
     *
     * @param string $param
     * @param mixed $value
     *
     * @return void
     */
    public function set($param, $value);

    /**
     * Get a parameter
     *
     * @param string $param
     *
     * @return mixed
     */
    public function get($param);

    /**
     * Check if a parameter exists.
     *
     * @param string $param
     *
     * @return boolean
     */
    public function has($param);

    /**
     * Removes a parameter
     *
     * @param string $param
     *
     * @return void
     */
    public function remove($param);

    /**
     * Gets all parameters as array
     *
     * @return array
     */
    public function all();

    /**
     * Merges two objects that implement `ParameterInterface`.
     *
     * @param ParameterInterface $parameters the collection to merge with.
     *
     * @return void
     */
    public function merge(ParameterInterface $parameters);
}
