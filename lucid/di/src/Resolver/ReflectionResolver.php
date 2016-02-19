<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Resolver;

use ReflectionClass;
use Lucid\DI\ContainerInterface;
use Lucid\Config\ParameterInterface;
use Lucid\DI\ContainerBuilderInterface;
use Lucid\DI\Exception\ResolverException;
use Lucid\DI\Exception\ContainerException;
use Lucid\DI\Definition\ServiceInterface;
use Lucid\DI\Definition\FactoryInterface;
use Lucid\DI\Reference\CallerInterface as CallerReferenceInterface;
use Lucid\DI\Reference\ServiceInterface as ServiceReferenceInterface;
use Lucid\DI\Parameter\Value;

/**
 * @class ReflectionResolver
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ReflectionResolver implements ResolverInterface
{
    /** @var array */
    private $resolving = [];

    /**
     * {@inheritdoc}
     */
    public function resolve($id, ContainerBuilderInterface $container, ParameterInterface $params = null)
    {
        $cid = spl_object_hash($container);

        if (isset($this->resolving[$cid][$id])) {
            throw ResolverException::circularReference($id);
        }

        $service = $container->getService($id);

        if ($service->isBlueprint() || $service->isInjected()) {
            throw ResolverException::notInstantiable($id);
        }

        $this->resolving[$cid][$id] = true;

        if ($this->isBoundService($service, $this->getResolvingIds($cid))) {
            throw ResolverException::boundService($id);
        }

        if ($service instanceof FactoryInterface) {
            $instance = $this->callFactory($id, $service, $container, $params);
        } else {
            if ($service->hasBluePrint()) {
                $service->mergeFromBluePrint($this->getService($service->getBlueprint()->getId()));
            }
            $instance = $this->resolveWithReflection($service, $container, $params);
        }

        $this->postResolveService($service, $instance, $container, $params);
        unset($this->resolving[$cid][$id]);

        return $instance;
    }

    /**
     * Get a list of all currently resolving service ids.
     *
     * @deprecated
     *
     * @return string[]
     */
    private function getResolvingIds($cid)
    {
        if (!isset($this->resolving[$cid])) {
            return [];
        }

        return array_keys($this->resolving[$cid]);
    }


    /**
     * Check if the service binding is invalid for the current resolve cicle.
     *
     * @param ServiceInterface $service
     *
     * @return boolean returns true if this service has bindings and is directly requested,
     * or the current resolve cicle doesn't allow to access this service. Otherwise, false.
     *
     * @return bool
     */
    private function isBoundService(ServiceInterface $service, array $ids)
    {
        return $service->isBound() &&
        (
            empty($ids) ||
            0 === count($d = array_intersect($service->getBindings(), $ids))
        );
    }

    /**
     * postResolveService
     *
     * @param ServiceInterface $service
     * @param object $instance
     *
     * @return void
     */
    private function postResolveService(
        ServiceInterface $service,
        $instance,
        ContainerInterface $container,
        ParameterInterface $params = null
    ) {
        foreach ($service->getSetters() as $setter) {
            list($method, $args) = $setter;
            call_user_func_array([$instance, $method], $this->resolveArguments($args, $container, $params));
        }

        foreach ($service->getCallers() as $caller) {
            $this->callCaller($caller, $container, $params);
        }
    }

    /**
     * callFactory
     *
     * @param mixed $id
     * @param FactoryInterface $factory
     *
     * @return void
     */
    private function callFactory(
        $id,
        FactoryInterface $factory,
        ContainerInterface $container,
        ParameterInterface $params = null
    ) {
        $arguments = $this->resolveArguments($factory->getArguments(), $container, $params);

        if (isset($this->factories[$id])) {
            $fn = $this->factories[$id];
        } elseif ($factory->isStatic()) {
            $fn = $factory->getClass() . '::' . $factory->getFactoryMethod();
        } else {
            $class = $factory->getClass();
            $fn = [new $class, $factory->getFactoryMethod()];
        }

        $this->factories[$id] = $fn;

        return call_user_func_array($fn, $arguments);
    }

    /**
     * resolveArguments
     *
     * @param array $arguments
     *
     * @return array
     */
    private function resolveArguments(array $arg, ContainerInterface $container, ParameterInterface $params = null)
    {
        $args = [];

        foreach ($arg as $key => $argument) {
            if ($argument instanceof ServiceReferenceInterface) {
                $arg = $container->get($argument->getId());
            } elseif ($argument instanceof CallerReferenceInterface) {
                $arg = $this->callCaller($argument);
            } elseif (is_array($argument)) {
                $arg = $this->resolveArguments($argument, $container, $params);
            } else {
                $arg = $this->getParameter($argument, $params);
            }

            $args[$key] = $arg;
        }

        return $args;
    }

    /**
     * Executes caller method calls.
     *
     * @return mixed|null
     */
    private function callCaller(
        CallerReferenceInterface $caller,
        ContainerInterface $container,
        ParameterInterface $params = null
    ) {
        if (!$container->hasService($id = $caller->getService()->getId())) {
            throw new ContainerException(sprintf('Caller service %d does not exist.', $id));
        }

        return call_user_func_array(
            [$container->get($caller->getService()->getId()), $caller->getMethod()],
            $this->resolveArguments($caller->getArguments(), $container, $params)
        );
    }

    /**
     * resolveFromDefinition
     *
     * @param ServiceInterface $definition
     *
     * @return Object
     */
    private function resolveWithReflection(
        ServiceInterface $definition,
        ContainerInterface $container,
        ParameterInterface $params = null
    ) {
        $reflection = new ReflectionClass($class = $definition->getClass());

        if ($reflectionConstructor = $reflection->getConstructor()) {
            $arguments = $definition->getArguments();
            $instance = $reflection->newInstanceArgs($this->resolveArguments($arguments, $container, $params));
        } else {
            $instance = new $class;
        }

        return $instance;
    }

    /**
     * getParameter
     *
     * @param string $param
     *
     * @return mixed
     */
    private function getParameter($param, ParameterInterface $params = null)
    {
        if (null === $params) {
            return $param;
        }

        if ($params instanceof ResolvableInterface) {
            return $params->resolveParam($param);
        }

        return $params->has($param) ? $params->get($param) : $param;
    }
}
