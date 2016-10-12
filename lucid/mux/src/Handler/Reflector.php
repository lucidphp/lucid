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
class Reflector
{
    /** @var int */
    const C_TYPE_ERROR           = 0;

    /** @var int */
    const C_TYPE_CLOSURE         = 1;

    /** @var int */
    const C_TYPE_FUNCTION        = 2;

    /** @var int */
    const C_TYPE_INSTANCE_METHOD = 3;

    /** @var int */
    const C_TYPE_STATIC_METHOD   = 4;

    /** @var int */
    const C_TYPE_INVOKED_OBJECT  = 5;

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
     * @return \ReflectionFunctionAbstract
     */
    public function getReflector() : \ReflectionFunctionAbstract
    {
        if (null === $this->reflector) {
            $this->reflector = $this->doGetReflector();
        }

        return $this->reflector;
    }

    /**
     * @param array ...$args
     * @return mixed
     */
    public function __invoke(...$args)
    {
        return ($this->handler)(...$args);
    }

    /**
     * invokeArgs
     *
     * @param array $args
     *
     * @return mixed
     */
    public function invokeArgs(array $args)
    {
        return $this->__invoke(...$args);
    }

    /**
     * isFunction
     *
     * @return bool
     */
    public function isFunction() : bool
    {
        return static::C_TYPE_FUNCTION === $this->getType();
    }

    /**
     * isClosure
     *
     * @return bool
     */
    public function isClosure() : bool
    {
        return static::C_TYPE_CLOSURE === $this->getType();
    }

    /**
     * isMethod
     *
     * @return bool
     */
    public function isMethod() : bool
    {
        return $this->isInstanceMethod() || $this->isStaticMethod();
    }

    /**
     * isInstanceMethod
     *
     * @return bool
     */
    public function isInstanceMethod() : bool
    {
        return static::C_TYPE_INSTANCE_METHOD === $this->getType();
    }

    /**
     * isStaticMethod
     *
     * @return bool
     */
    public function isStaticMethod() : bool
    {
        return static::C_TYPE_STATIC_METHOD === $this->getType();
    }

    /**
     * isInvokedObject
     *
     * @return bool
     */
    public function isInvokedObject() : bool
    {
        return static::C_TYPE_INVOKED_OBJECT === $this->getType();
    }

    /**
     * getType
     *
     * @return int
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
            return static::C_TYPE_CLOSURE;
        }

        $callable = $this->explodeCallable($method);

        if (1 === count($callable)) {
            if (is_object($callable[0])) {
                return static::C_TYPE_INVOKED_OBJECT;
            }
            return static::C_TYPE_FUNCTION;
        } else {
            if (is_object($callable[0])) {
                return static::C_TYPE_INSTANCE_METHOD;
            }

            if (is_string($callable[0])) {
                return static::C_TYPE_STATIC_METHOD;
            }
        }

        return static::C_TYPE_ERROR;
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
            case static::C_TYPE_FUNCTION:
            case static::C_TYPE_CLOSURE:
                return new \ReflectionFunction($this->handler);
            case static::C_TYPE_STATIC_METHOD:
            case static::C_TYPE_INSTANCE_METHOD:
                return new \ReflectionMethod($this->getClass(), $this->getMethod());
            case static::C_TYPE_INVOKED_OBJECT:
                return new \ReflectionMethod($this->getClass(), '__invoke');
            case static::C_TYPE_ERROR:
                throw new \Exception;
                break;
        }
    }
}
