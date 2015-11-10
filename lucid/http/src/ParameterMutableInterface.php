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
 * @class ParameterMutableInterface
 * @see ParameterInterface
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ParameterMutableInterface extends ParameterInterface
{
    /**
     * Sets a new item.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value);

    /**
     * Adds a new item.
     *
     * Depending on the implementation, this may also behave as a shortcut to
     * ParameterMutableInterface::add()
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function add($key, $value);

    /**
     * Removes an item.
     *
     * @param string $key
     */
    public function remove($key);
}
