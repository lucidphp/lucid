<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Loader;

/**
 * @interface ResolverInterface
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResolverInterface
{
    /**
     * Adds a loader.
     *
     * @param LoaderInterface $loader
     *
     * @return void
     */
    public function addLoader(LoaderInterface $loader);

    /**
     * Resolves a loader for a given resource
     *
     * @param mixed $resource
     *
     * @return \Lucid\Resource\LoaderInterface
     */
    public function resolve($resource);

    /**
     * Returns an array of loaders.
     *
     * @return array `LoaderInterface[]`
     */
    public function all();
}
