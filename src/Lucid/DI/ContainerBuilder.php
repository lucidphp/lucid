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

use ReflectionClass;
use Lucid\Config\ResolvableInterface;
use Lucid\DI\Resolver\ResolverInterface;
use Lucid\DI\Resolver\ReflectionResolver;
use Lucid\DI\Exception\ContainerException;
use Interop\Container\Exception\ContainerException as InteropContainerException;
use Lucid\DI\Reference\CallerReferenceInterface;
use Lucid\DI\Reference\ServiceReferenceInterface;

/**
 * @class ContainerBuilder
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerBuilder extends Container implements ContainerBuilderInterface
{
    /**
     * factories
     *
     * @var callable[]
     */
    private $factories = [];

    /**
     * definitions
     *
     * @var ServiceInteface[]
     */
    private $definitions = [];

    /**
     * resolver
     *
     * @var ResolverInterface
     */
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
        parent::__construct($params, $aliases, $cmap, $icmap, $synced);
        $this->resolver = $resolver;
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
        $resolver = $this->resolver;

        if ($exists = $this->hasService($id = $this->getAlias($id))) {
            if (null !== $resolver && $this->isBoundService($service = $this->getService($id))) {
                throw ContainerException::undefinedService($id);
            }
        }

        try {
            return parent::get($id);
        } catch (InteropContainerException $e) {
        }

        if (!$exists) {
            throw NotFoundException::serviceUndefined($id);
        }

        if (null === $resolver) {
            throw ContainerException::notResolveable($id);
        }

        $instance = $resolver->resolve($id, $service = $this->getService($id), $this->parameters);

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

    /**
     * isBoundService
     *
     * @param ServiceInterface $service
     *
     * @return bool
     */
    private function isBoundService(ServiceInterface $service)
    {
        $ids = $this->resolver->getResolvingIds();

        return $service->isBound() &&
        (
            empty($ids) ||
            0 === count($d = array_intersect($service->getBindings(), $ids))
        );
    }
}
