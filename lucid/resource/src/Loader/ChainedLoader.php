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

use Lucid\Resource\Exception\LoaderException;

/**
 * @class ChainedLoader
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ChainedLoader implements LoaderInterface
{
    /**
     * loaders
     *
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * Constructor.
     *
     * @param array $loaders
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->setResolver($resolver);
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
        foreach ($this->loaders() as $loader) {
            if ($loader->supports($resource)) {
                return $loader;
            }
        }

        throw new LoaderException('No matching loader found.');
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource)
    {
        return $this->resolve($resource)->load($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function import($resource)
    {
        return $this->load($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource)
    {
        foreach ($this->loaders() as $loader) {
            if ($loader->supports($resource)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addListener(ListenerInterface $listener)
    {
        foreach ($this->loaders() as $loader) {
            $loader->addListener($listener);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener(ListenerInterface $listener)
    {
        foreach ($this->loaders() as $loader) {
            $loader->removeListener($listener);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;

        foreach ($this->loaders() as $loader) {
            $loader->setResolver($resolver);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * loaders
     *
     * @return array `LoaderInterface[]`
     */
    private function loaders()
    {
        return $this->resolver->all();
    }
}
