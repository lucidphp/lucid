<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI;

use Lucid\DI\Resolver\ResolverInterface;
use Lucid\DI\Resolver\ReflectionResolver;
use Lucid\DI\Exception\ResolverException;
use Lucid\DI\Exception\NotFoundException;
use Lucid\DI\Exception\ContainerException;
use Interop\Container\Exception\ContainerException as InteropContainerException;

/**
 * @class ContainerBuilder
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerBuilder extends Container implements ContainerBuilderInterface
{
    /** @var callable[] */
    private $factories = [];

    /** @var ServiceInteface[] */
    private $definitions = [];

    /** @var Lucid\DI\Resolver\ResolverInterface */
    private $resolver;

    /**
     * Constructor.
     *
     * @param ResolverInterafce $resolver
     * @param ParameterInterface $params
     * @param array $aliases
     * @param array $cmap
     * @param array $icmap
     * @param array $synced
     */
    public function __construct(
        ResolverInterafce $resolver = null,
        ParameterInterface $params = null,
        array $aliases = [],
        array $cmap = [],
        array $icmap = [],
        array $synced = []
    ) {
        parent::__construct(null, $params, $aliases, $cmap, $icmap, $synced);
        $this->resolver = $resolver ?: new ReflectionResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function define($id, $class = null, array $arguments = [], $scope = Scope::SINGLETON)
    {
        $this->register($id, $servive = new Service($class, $arguments, $scope));

        return $service;
    }

    /**
     * {@inheritdoc}
     */
    public function register($id, ServiceInterface $service)
    {
        $this->definitions[$id] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function newService($class = null, array $arguments = [])
    {
        return new Service($class, $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function newFactory($class, $method, $static = true)
    {
        return new Factory($class, $method, $static);
    }

    /**
     * {@inheritdoc}
     */
    public function hasService($id)
    {
        return isset($this->definitions[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function getService($id)
    {
        return $this->definitions[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getServices()
    {
        return $this->definitions;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        try {
            return parent::get($id);
        } catch (InteropContainerException $e) {
        }

        if (null === $this->resolver || false === $this->hasService($id = $this->getId($id))) {
            throw NotFoundException::serviceUndefined($id);
        }

        try {
            $instance = $this->resolver->resolve($id, $this, $this->parameters);
        } catch (ResolverException $e) {
            throw new ContainerException($e->getMessage(), $e);
        }

        /** @var ServiceInteface */
        $service = $this->getService($id);

        if (Scope::SINGLETON === $service->getScope()) {
            $this->instances[$id] = $instance;
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerReflectorInterface $reflector, ContainerTargetInterface $target)
    {
    }
}
