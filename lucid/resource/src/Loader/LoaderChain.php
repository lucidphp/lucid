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
 * @class LoaderChain
 *
 * @package Lucid\Resource\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class LoaderChain implements LoaderInterface
{
    /**
     * loaders
     *
     * @var LoaderInterface[]
     */
    private $loaders;

    /**
     * Constructor.
     *
     * @param array $loaders
     */
    public function __construct(array $loaders)
    {
        $this->setLoaders($loaders);
    }

    /**
     * Resolve a loader.
     *
     * @param mixed $resource
     *
     * @return LoaderInterface
     */
    public function resolve($resource)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($resource)) {
                return $loader;
            }
        }

        throw new \RuntimeException('No matching loader found.');
    }

    /**
     * addLoader
     *
     * @param LoaderInterace $loader
     *
     * @return void
     */
    public function addLoader(LoaderInterace $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * setLoaders
     *
     * @param array $loaders
     *
     * @return void
     */
    public function setLoaders(array $loaders)
    {
        $this->loader = [];

        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource)
    {
        return $this->resolve($loader)->load($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function import($resource)
    {
        return $this->resolve($loader)->load($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function suports($resource)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($resource)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addListener(LoaderListenerInterface $listener)
    {
        foreach ($this->loaders as $loader) {
            $loader->addListener($listener);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener(LoaderListenerInterface $listener)
    {
        foreach ($this->loaders as $loader) {
            $loader->removeListener($listener);
        }
    }
}
