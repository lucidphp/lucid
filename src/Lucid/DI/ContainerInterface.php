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

use Interop\Container\ContainerInterface as InteropContainer;

/**
 * A minimal DI container interface.
 *
 * @interface ContainerInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ContainerInterface extends InteropContainer
{
    const NULL_ON_MISSING = 0;
    const EXCEPTION_ON_MISSING = 1;
    const EXCEPTION_ON_DUPLICATE = 0;
    const FORCE_REPLACE_ON_DUPLICATE = 1;

    /**
     * Sests an object instance
     *
     * @param string $id
     * @param object $implementation
     * @param int $forceReplace
     *
     * @return void
     */
    public function set($id, $implementation, $forceReplace = self::EXCEPTION_ON_DUPLICATE);

    /**
     * replace
     *
     * @param string $id
     * @param object $implementation
     * @param int $behaves
     *
     * @return void
     */
    public function replace($id, $implementation, $behaves = self::EXCEPTION_ON_MISSING);
}
