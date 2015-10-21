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

use SplObjectStorage;

/**
 * @class AbstractLoader
 *
 * @package Lucid\Resource\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractLoader implements LoaderInterface
{
    /**
     * listeners
     *
     * @var SplObjectStorage
     */
    private $listeners;

    /**
     * {@inheritdoc}
     */
    public function addListener(LoaderListenerInterface $listener)
    {
        $this->getListeners()->attach($listener);
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener(LoaderListenerInterface $listener)
    {
        $this->getListeners()->detach($listener);
    }

    /**
     * notify
     *
     * @return void
     */
    protected function notify($resource)
    {
        foreach ($this->getListeners() as $listener) {
            $listener->onLoaded($resource);
        }
    }

    /**
     * loadResource
     *
     * @param mixed $resource
     *
     * @return void
     */
    private function loadResource($resource)
    {
        $res = $this->doLoad($resource);
        $this->notify($resource);

        return $res;
    }

    /**
     * getObservers
     *
     * @return \SplObjectStorage
     */
    private function getListeners()
    {
        if (null === $this->listeners) {
            $this->listeners = new SplObjectStorage;
        }

        return $this->listeners;
    }
}
