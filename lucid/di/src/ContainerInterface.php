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
    /** @var int */
    const NULL_ON_MISSING            = 0;

    /** @var int */
    const EXCEPTION_ON_MISSING       = 1;

    /** @var int */
    const EXCEPTION_ON_DUPLICATE     = 10;

    /** @var int */
    const FORCE_REPLACE_ON_DUPLICATE = 20;

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
     * Replaces a service instance.
     *
     * @param string $id
     * @param object $implementation
     * @param int $behaves
     *
     * @return void
     */
    public function replace($id, $implementation, $behaves = self::EXCEPTION_ON_MISSING);

    /**
     * Associates an alias with a service id.
     *
     * @param string $id
     * @param string $alias
     *
     * @return void
     */
    public function setAlias($id, $alias);

    /**
     * Returns the service id associated with an alias.
     *
     * @param string $alias
     *
     * @return string
     */
    public function getId($alias);

    /**
     * Adds a delegating container.
     *
     * @param InteropContainer $container
     *
     * @return void
     */
    public function delegate(InteropContainer $container);
}
