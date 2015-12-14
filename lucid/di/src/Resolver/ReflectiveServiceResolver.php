<?php

/*
 * This File is part of the Lucid\DI\Resolver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Resolver;

/**
 * @class ReflectiveServiceResolver
 *
 * @package Lucid\DI\Resolver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ReflectiveServiceResolver
{
    private $container;
    private $resolving;

    public function __construct(ContainerBuilderInterface $container)
    {
        $this->container = $container;
        $this->container->setResolver($this);
        $this->resolving = [];
    }

    public function resolve($id, ParameterInterface $parameters = null)
    {
        if (isset($this->resolving[$id])) {
            throw ContainerException::circularReference($id);
        }

        $service = $this->container->getService($id);

        if ($service->isBlueprint() || $service->isInjected()) {
            throw ContainerException::notInstantiable($id);
        }

        $this->resolving[$id] = true;

        if ($service instanceof FactoryInterface) {
            $instance = $this->callFactory($id, $service, $container);
        } else {
            if ($service->hasBluePrint()) {
                $service->mergeFromBluePrint($this->getService($service->getBlueprint()->getId()));
            }

            $instance = $this->resolveWithReflection($service);
        }

        $this->postResolveService($service, $instance);

        unset($this->resolving[$id]);

        return $instance;
    }
}
