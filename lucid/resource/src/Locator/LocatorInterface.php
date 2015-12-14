<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Locator;

/**
 * @interface LocatorInterface
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface LocatorInterface
{
    /**
     * Locate a resource file
     *
     * @param string  $name typically the filename
     * @param bool $collect weather to collection all files within the given
     * paths or return the first match.
     *
     * @return string|array
     */
    public function locate($name, $collect = false);

    /**
     * Adds a resource path.
     *
     * @param string $path a resource path.
     *
     * @return void
     */
    public function addPath($path);

    /**
     * Adds resource paths.
     *
     * @param array $paths a collection of resource paths.
     *
     * @return void
     */
    public function addPaths(array $paths);

    /**
     * Sets resource paths.
     *
     * @param array $paths a collection of resource paths.
     *
     * @return void
     */
    public function setPaths(array $paths);

    /**
     * Set the location root path.
     *
     * @param string $root
     *
     * @return void
     */
    public function setRootPath($root);
}
