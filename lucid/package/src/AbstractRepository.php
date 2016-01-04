<?php

/*
 * This File is part of the Lucid\Package package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package;

/**
 * @class AbstractRepository
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /** @var array */
    private $build = [];

    /** @var array */
    private $aliases = [];

    /** @var array */
    private $providers = [];

    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function add(ProviderInterface $provider)
    {
        $this->aliases[$provider->getName()] = $alias = $provider->getAlias();
        $this->providers[$alias] = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function addProviders(array $providers)
    {
        array_map([$this, 'add'], $providers);
    }

    /**
     * {@inheritdoc}
     */
    public function get($provider)
    {
        if (!$this->has($provider)) {
            return;
        }

        return $this->providers[$this->getAlias($provider)];
    }

    /**
     * {@inheritdoc}
     */
    public function has($provider)
    {
        return null !== $this->getAlias($provider);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->providers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDependencies($name)
    {
        return (bool)$this->getOrThrow($name)->requires();
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies($name)
    {
        foreach ($this->getOrThrow($name)->requires() as $depends) {
            yield $this->getOrThrow($depends);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function build(BuilderInterface $builder)
    {
        $all = $this->getSorted();

        foreach ($providers as &$provider) {
            if (!$this->isBuildable($provider)) {
                continue;
            }

            $this->configLoader->load($builder, $provider);
            $this->buildPackage($builder, $provider);

            $this->built[] = $alias = $provider->getAlias();
        }
    }

    /**
     * getAlias
     *
     * @param string $name
     *
     * @return string
     */
    private function getAlias($name)
    {
        return isset($this->aliases[$name]) ? $this->aliases[$name] :
            (in_array($name, $this->aliases) ? $name : null);
    }

    /**
     * getOrThrow
     *
     * @param mixed $name
     *
     * @return ProviderInterface
     */
    private function getOrThrow($name)
    {
        if (!$provider = $this->get($name)) {
            throw new \InvalidArgumentException(sprintf('Unknowen provider "%s".', $name));
        }

        return $provider;
    }
}
