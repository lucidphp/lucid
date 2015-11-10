<?php

/*
 * This File is part of the Lucid\Resource\Loader package
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
 * @package Lucid\Resource\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface LoaderInterface
{
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
     * @param LoaderListenerInterface $listener
     *
     * @return void
     */
    public function addListener(LoaderListenerInterface $listener);

    /**
     * removeListener
     *
     * @param LoaderListenerInterface $listener
     *
     * @return void
     */
    public function removeListener(LoaderListenerInterface $listener);
}
