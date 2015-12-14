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

use SplObjectStorage;

/**
 * @class AbstractLoader
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractLoader implements LoaderInterface
{
    /** @var SplObjectStorage */
    private $listeners;

    /**
     * {@inheritdoc}
     */
    public function addListener(ListenerInterface $listener)
    {
        $this->getListeners()->attach($listener);
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener(ListenerInterface $listener)
    {
        $this->getListeners()->detach($listener);
    }

    /**
     * Calls 'onLoaded' on all listeners.
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
     * Loads a resource.
     *
     * @param mixed $resource
     *
     * @return void
     */
    protected function loadResource($resource)
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
