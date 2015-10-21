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
 * @interface ResolvableInterface
 *
 * @package Lucid\Config
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResolvableInterface
{
    /**
     * resolve
     *
     *
     * @return void
     */
    public function resolve();

    /**
     * resolveParam
     *
     * @param mixed $param
     *
     * @return void
     */
    public function resolveParam($param);

    /**
     * resolveString
     *
     * @param mixed $string
     *
     * @return void
     */
    public function resolveString($string);

    /**
     * isResolved
     *
     * @return boolean
     */
    public function isResolved();

    /**
     * setUnresolved
     *
     * @return void
     */
    public function setUnresolved();
}
