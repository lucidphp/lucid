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
use Lucid\Resource\ResourceInterface;
use Lucid\Resource\Exception\LoaderException;

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

    /** @var ResourceInterface */
    private $resolver;

    /**
     * {@inheritdoc}
     */
    public function load($resource, $any = self::LOAD_ONE)
    {
        foreach ($this->findResource($resource, $any)->all() as $file) {
            $this->loadResource($file);
        }
    }

    /**
     * {@inheritdoc}
     * @throws LoaderException
     */
    public function import($resource)
    {
        if ($this->supports($resource)) {
            return $this->load($resource);
        }

        try {
            $loader = $this->getResolver()->resolve($resource);
        } catch (\Exception $e) {
            throw LoaderException::missingLoader();
        }
    }

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
     * {@inheritdoc}
     */
    public function setResolver(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
        return $this->resolver;
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

    abstract protected function doLoad($resource);

    /**
     * Loads a resource.
     *
     * @param mixed $resource
     *
     * @return void
     */
    protected function loadResource(ResourceInterface $resource)
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
