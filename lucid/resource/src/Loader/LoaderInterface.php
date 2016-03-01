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
 * @class LoaderInterface
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface LoaderInterface
{
    /** @var bool */
    const LOAD_ALL = true;

    /** @var bool */
    const LOAD_ONE = false;

    /**
     * load
     *
     * @param mixed $resource
     *
     * @return mixed
     */
    public function load($resource);

    /**
     * import
     *
     * @param mixed $resource
     *
     * @return void
     */
    public function import($resource);

    /**
     * supports
     *
     * @param mixed $resource
     *
     * @return boolean
     */
    public function supports($resource);

    /**
     * addListener
     *
     * @param ListenerInterface $listener
     *
     * @return void
     */
    public function addListener(ListenerInterface $listener);

    /**
     * removeListener
     *
     * @param ListenerInterface $listener
     *
     * @return void
     */
    public function removeListener(ListenerInterface $listener);

    /**
     * setResolver
     *
     * @param ResolverInterface $resolver
     *
     * @return void
     */
    public function setResolver(ResolverInterface $resolver);

    /**
     * getResolver
     *
     * @return ResolverInterface
     */
    public function getResolver();
}
