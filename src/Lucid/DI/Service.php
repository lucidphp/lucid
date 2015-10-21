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

use Lucid\DI\Reference\CallerReferenceInterface;
use Lucid\DI\Reference\ServiceReferenceInterface;

/**
 * @class Service
 * @see ServiceInterface
 * @see AttributeableInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Service implements ServiceInterface, AttributeableInterface
{
    private $class;
    private $arguments;
    private $scope;
    private $parent;
    private $blueprint;
    private $callers;
    private $setters;
    private $attributes;
    private $bindings;
    private $injected;

    /**
     * Constructor.
     *
     * @param string $class
     * @param array  $arguments
     * @param string $scope
     */
    public function __construct($class = null, array $arguments = [], $scope = Scope::SINGLETON)
    {
        $this->class = $class;
        $this->arguments = $arguments;
        $this->scope = $scope;
        $this->attributes = [];
        $this->bindings = [];
        $this->callers = [];
        $this->setters = [];
    }

    /**
     * {@inheritdoc}
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getArgument($index = 0)
    {
        $this->inRange($index, $this->arguments);

        return $this->arguments[(int)$index];
    }

    /**
     * {@inheritdoc}
     */
    public function setArgument($value, $index = 0)
    {
        $this->inRange($index, $this->arguments) && $this->arguments[(int)$index] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($key, $default = null)
    {
        if (!array_key_exists($key, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * {@inheritdoc}
     */
    public function hasScope($scope)
    {
        return $scope === $this->scope;
    }

    /**
     * {@inheritdoc}
     */
    public function isBlueprint()
    {
        return (bool)$this->blueprint;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsBlueprint($bp)
    {
        $this->blueprint = (bool)$bp;
    }

    /**
     * {@inheritdoc}
     */
    public function setBluePrint(ServiceReferenceInterface $blueprint)
    {
        $this->parent = $blueprint;
    }

    /**
     * {@inheritdoc}
     */
    public function getBluePrint()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function hasBluePrint()
    {
        return null !== $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function isBound()
    {
        return 0 < count($this->bindings);
    }

    /**
     * {@inheritdoc}
     */
    public function isBoundTo($reference)
    {
        return in_array((string)$reference, $this->bindings);
    }

    /**
     * {@inheritdoc}
     */
    public function addBinding(ServiceReferenceInterface $binding)
    {
        $this->bindings[] = $binding;
    }

    /**
     * {@inheritdoc}
     */
    public function setBindings(array $bindings)
    {
        $this->bindings = [];

        foreach ($bindings as $binding) {
            $this->addBinding($binding);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * {@inheritdoc}
     */
    public function setInjected($injected)
    {
        $this->injected = (bool)$injected;
    }

    /**
     * {@inheritdoc}
     */
    public function isInjected()
    {
        return (bool)$this->injected;
    }

    /**
     * {@inheritdoc}
     */
    public function setCallers(array $callers)
    {
        $this->callers = [];

        foreach ($callers as $caller) {
            $this->calls($caller);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCallers()
    {
        return $this->callers;
    }

    /**
     * {@inheritdoc}
     */
    public function calls(CallerReferenceInterface $caller)
    {
        $this->callers[] = $caller;
    }

    /**
     * {@inheritdoc}
     */
    public function getSetters()
    {
        return $this->setters;
    }

    /**
     * {@inheritdoc}
     */
    public function setSetters(array $setters)
    {
        $this->setters = [];

        foreach ($setters as $setter) {
            list($method, $args) = array_pad($setter, 2, []);
            $this->sets($method, $args);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sets($method, array $arguments = [])
    {
        $this->setters[] = [$method, $arguments];
    }

    /**
     * {@inheritdoc}
     */
    public function mergeFromBluePrint(ServiceInterface $bp)
    {
        $this->class = $bp->getClass() ?: $this->class;
        $this->arguments = $this->mergeBlueprintArguments($bp);
    }

    /**
     * mergeBlueprintArguments
     *
     * @param ServiceInterface $bp
     *
     * @return array
     */
    private function mergeBlueprintArguments(ServiceInterface $bp)
    {
        if (!(bool)$this->getArguments()) {
            return $bp->getArguments();
        }

        $args = $bg->getArguments();

        foreach ($this->getArguments() as $key => $argument) {
            $args[$key] = $argument;
        }

        return $args;
    }

    /**
     * inRange
     *
     * @param mixed $index
     * @param array $args
     *
     * @return boolean
     */
    private function inRange($index, array $args)
    {
        if (($i = (int)$index) > max(0, count($args) - 1) || $i < 0) {
            throw new \OutOfBoundsException(sprintf('No arguments in list at index %d.', $i));
        }

        return true;
    }
}
