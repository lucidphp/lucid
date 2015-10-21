<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI;

/**
 * @interface AttributeableInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface AttributeableInterface
{
    /**
     * Sets an attribute
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setAttribute($key, $value);

    /**
     * Gets an Attribute
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed|null
     */
    public function getAttribute($key, $default = null);

    /**
     * getAttributes
     *
     * @return array
     */
    public function getAttributes();
}
