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
 * @class Resolver
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Resolver implements ResolverInterface
{
    /** @var array `LoaderInterface[]` */
    private $loaders;

    /**
     * Constructor.
     *
     * @param array $loaders `LoaderInterface[]`
     */
    public function __construct(array $loaders = [])
    {
        $this->loaders = [];

        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addLoader(LoaderInterface $loader)
    {
        $loader->setResolver($this);
        $this->loaders[] = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($resource)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($resource)) {
                return $loader;
            }
        }

        throw LoaderException::missingLoader($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->loaders;
    }
}
