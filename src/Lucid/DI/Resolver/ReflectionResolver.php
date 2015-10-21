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

use ReflectionClass;
use Lucid\DI\ServiceInterface;
use Lucid\DI\FactoryInterface;
use Lucid\Config\ParameterInterface;
use Lucid\DI\ContainerBuilderInterface;
use Lucid\DI\Exception\ContainerException;
use Lucid\DI\Reference\CallerReferenceInterface;
use Lucid\DI\Reference\ServiceReferenceInterface;

/**
 * @class ReflectionResolver
 *
 * @package Lucid\DI\Resolver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ReflectionResolver implements ResolverInterface
{
    /**
     * resolving
     *
     * @var array
     */
    private $resolving = [];

    /**
     * {@inheritdoc}
     */
    public function resolve($id, ContainerBuilderInterface $container, ParameterInterface $params = null)
    {
        if (isset($this->resolving[$id])) {
            throw ContainerException::circularReference($id);
        }

        $service = $container->getService($id);

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

    /**
     * Get a list of all currently resolving service ids.
     *
     * @return array[string]
     */
    public function getResolvingIds()
    {
        return array_keys($this->resolving);
    }

    /**
     * postResolveService
     *
     * @param ServiceInterface $service
     * @param object $instance
     *
     * @return void
     */
    private function postResolveService(ServiceInterface $service, $instance)
    {
        foreach ($service->getSetters() as $setter) {
            list ($method, $args) = $setter;
            call_user_func_array([$instance, $method], $this->resolveArguments($args));
        }

        foreach ($service->getCallers() as $caller) {
            $this->callCaller($caller);
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
    private function callFactory($id, FactoryInterface $factory)
    {
        $arguments = $this->resolveArguments($factory->getArguments());

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
    private function resolveArguments(array $arguments, ParameterInterface $params = null)
    {
        $args = [];

        foreach ($arguments as $key => $argument) {
            if ($argument instanceof ServiceReferenceInterface) {
                $arg = $this->get($argument->getId());
            } elseif ($argument instanceof CallerReferenceInterface) {
                $arg = $this->callCaller($argument);
            } elseif (is_array($argument)) {
                $arg = $this->resolveArguments($argument, $params);
            } else {
                $arg = $this->getParameter($argument, $params);
            }

            $args[$key] = $arg;
        }

        return $args;
    }

    /**
     * callCaller
     *
     * @param CallerReferenceInterface $caller
     *
     * @return mixed|null
     */
    private function callCaller(CallerReferenceInterface $caller, ParameterInterface $params = null)
    {
        if (!$this->hasService($id = $caller->getService()->getId())) {
            throw new ContainerException(sprintf('Caller service %d does not exist.', $id));
        }

        return call_user_func_array(
            [$this->get($id), $caller->getMethod()],
            $this->resolveArguments($caller->getArguments(), $params)
        );
    }

    /**
     * resolveFromDefinition
     *
     * @param ServiceInterface $definition
     *
     * @return Object
     */
    private function resolveWithReflection(ServiceInterface $definition)
    {
        $reflection = new ReflectionClass($class = $definition->getClass());

        if ($reflectionConstructor = $reflection->getConstructor()) {
            $arguments = $definition->getArguments();
            $instance = $reflection->newInstanceArgs($this->resolveArguments($arguments));
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
    private function getParameter($param, ParameterInterface $parameters = null)
    {
        if (null === $parameters) {
            return $param;
        }

        if ($parameters instanceof ResolvableInterface) {
            return $parameters->resolveParam($param);
        }

        return $parameters->get($param);
    }
}
