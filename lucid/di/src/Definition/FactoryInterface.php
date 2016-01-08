<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Definition;

/**
 * @interface FactoryInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface FactoryInterface extends ServiceInterface
{
    /**
     * getMethod
     *
     * @return string|calleble
     */
    public function getFactoryMethod();

    /**
     * isStatic
     *
     * @return boolean
     */
    public function isStatic();
}
