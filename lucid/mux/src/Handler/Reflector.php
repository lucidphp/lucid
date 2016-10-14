<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Handler;

/**
 * @class Reflector
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Reflector implements ReflectorInterface
{
    /** @var int */
    private $type;

    /** @var callable */
    private $handler;

    /** @var \Reflector **/
    private $reflector;

    /** @var array  */
    private $args;

    /**
     * Constructor.
     *
     * @param callable $handler
     * @param array $args
     */
    public function __construct(callable $handler, ...$args)
    {
        $this->handler = $handler;
        $this->args = $args;
    }

    /**
     * {@inheritdoc}
     */
    public function getReflector() : \ReflectionFunctionAbstract
    {
        if (null === $this->reflector) {
            $this->reflector = $this->doGetReflector();
        }

        return $this->reflector;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(...$args)
    {
        return ($this->handler)(...$args);
    }

    /**
     * {@inheritdoc}
     */
    public function invokeArgs(array $args)
    {
        return $this->__invoke(...$args);
    }

    /**
     * {@inheritdoc}
     */
    public function isFunction() : bool
    {
        return self::C_TYPE_FUNCTION === $this->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function isClosure() : bool
    {
        return self::C_TYPE_CLOSURE === $this->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function isMethod() : bool
    {
        return $this->isInstanceMethod() || $this->isStaticMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function isInstanceMethod() : bool
    {
        return self::C_TYPE_INSTANCE_METHOD === $this->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function isStaticMethod() : bool
    {
        return self::C_TYPE_STATIC_METHOD === $this->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function isInvokedObject() : bool
    {
        return self::C_TYPE_INVOKED_OBJECT === $this->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function getType() : int
    {
        if (null === $this->type) {
            $this->type = $this->getCallableType($this->handler);
        }

        return $this->type;
    }

    /**
     * getCallableType
     *
     * @param callable $method
     *
     * @return int
     */
    private function getCallableType(callable $method) : int
    {
        if ($method instanceof \Closure) {
            return self::C_TYPE_CLOSURE;
        }

        $callable = $this->explodeCallable($method);

        if (1 === count($callable)) {
            if (is_object($callable[0])) {
                return self::C_TYPE_INVOKED_OBJECT;
            }
            return self::C_TYPE_FUNCTION;
        } else {
            if (is_object($callable[0])) {
                return self::C_TYPE_INSTANCE_METHOD;
            }

            if (is_string($callable[0])) {
                return self::C_TYPE_STATIC_METHOD;
            }
        }

        return self::C_TYPE_ERROR;
    }

    /**
     * getClass
     *
     * @return string
     */
    private function getClass() : string
    {
        $parts = $this->explodeCallable($this->handler);

        if ($this->isStaticMethod()) {
            return $parts[0];
        }

        return get_class($parts[0]);
    }

    /**
     * getMethod
     *
     * @return string
     */
    private function getMethod() : string
    {
        if (is_array($this->handler) && 2 === sizeof($this->handler)) {
            return $this->handler[1];
        }

        if ($this->isStaticMethod()) {
            list (, $method) = explode('::', $this->handler);

            return $method;
        }

        return '';
    }

    /**
     * explodeCallable
     *
     * @param callable $method
     *
     * @return array
     */
    private function explodeCallable(callable $method)
    {
        if (is_array($method)) {
            return $method;
        }

        if (is_string($method)) {
            return explode('::', $method);
        }

        return [$method];
    }

    /**
     * @return \ReflectionFunctionAbstract
     * @throws \Exception
     */
    private function doGetReflector() : \ReflectionFunctionAbstract
    {
        switch ($this->getType()) {
            case self::C_TYPE_FUNCTION:
            case self::C_TYPE_CLOSURE:
                return new \ReflectionFunction($this->handler);
            case self::C_TYPE_STATIC_METHOD:
            case self::C_TYPE_INSTANCE_METHOD:
                return new \ReflectionMethod($this->getClass(), $this->getMethod());
            case self::C_TYPE_INVOKED_OBJECT:
                return new \ReflectionMethod($this->getClass(), '__invoke');
            case self::C_TYPE_ERROR:
                throw new \Exception;
                break;
        }
    }
}
