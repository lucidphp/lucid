<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Definition;

use Lucid\DI\Scope;
use Lucid\DI\AttributeableInterface;
use Lucid\DI\Reference\CallerInterface as CallerReferenceInterface;
use Lucid\DI\Reference\ServiceInterface as ServiceReferenceInterface;

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
    /** @var string */
    private $class;

    /** @var array */
    private $arguments;

    /** @var string */
    private $scope;

    /** @var string */
    private $parent;

    /** @var array */
    private $callers;

    /** @var array */
    private $setters;

    /** @var array */
    private $attributes;

    /** @var \Lucid\DI\Reference\ServiceInterface[] */
    private $bindings;

    /** @var bool */
    private $injected;

    /** @var \Lucid\DI\Reference\ServiceInterface */
    private $blueprint;

    /**
     * Constructor.
     *
     * @param string $class
     * @param array  $arguments
     * @param string $scope
     */
    public function __construct($class = null, array $arguments = [], Scope $scope = null)
    {
        $this->class      = $class;
        $this->arguments  = $arguments;
        $this->scope      = $scope ?: new Scope;
        $this->attributes = [];
        $this->bindings   = [];
        $this->callers    = [];
        $this->setters    = [];
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

        array_map([$this, 'addBinding'], $bindings);
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

        array_map([$this, 'calls'], $callers);
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

        array_map(function ($setter) {
            list($method, $args) = array_pad($setter, 2, []);
            $this->sets($method, $args);
        }, $setters);
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
        if (!(bool)($args = $this->getArguments())) {
            return $bp->getArguments();
        }

        return array_merge($bp->getArguments(), $args);
    }

    /**
     * inRange
     *
     * @param mixed $index
     * @param array $args
     *
     * @return bool
     */
    private function inRange($index, array $args)
    {
        if (($i = (int)$index) > max(0, count($args) - 1) || $i < 0) {
            throw new \OutOfBoundsException(sprintf('No arguments in list at index %d.', $i));
        }

        return true;
    }
}
