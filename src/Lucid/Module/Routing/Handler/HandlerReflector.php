<?php

/*
 * This File is part of the Lucid\Module\Routing\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Handler;

/**
 * @class HandlerReflector
 *
 * @package Lucid\Module\Routing\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class HandlerReflector
{
    const C_TYPE_ERROR = 0;
    const C_TYPE_CLOSURE = 1;
    const C_TYPE_FUNCTION = 2;
    const C_TYPE_INSTANCE_METHOD = 3;
    const C_TYPE_STATIC_METHOD = 4;
    const C_TYPE_INVOKED_OBJECT = 5;

    private $type;
    private $handler;
    private $reflector;

    /**
     * Constructor.
     *
     * @param callable $handler
     */
    public function __construct(callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * getReflector
     *
     * @return ReflectionFunctionAbstract
     */
    public function getReflector()
    {
        if (null === $this->reflector) {
            $this->reflector = $this->doGetReflector();
        }

        return $this->reflector;
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
        return call_user_func_array($this->handler, $args);
    }

    /**
     * isFunction
     *
     * @return boolean
     */
    public function isFunction()
    {
        return static::C_TYPE_FUNCTION === $this->getType();
    }

    /**
     * isClosure
     *
     * @return boolean
     */
    public function isClosure()
    {
        return static::C_TYPE_CLOSURE === $this->getType();
    }

    /**
     * isMethod
     *
     * @return boolean
     */
    public function isMethod()
    {
        return $this->isInstanceMethod() || $this->isStaticMethod();
    }

    /**
     * isInstanceMethod
     *
     * @return boolean
     */
    public function isInstanceMethod()
    {
        return static::C_TYPE_INSTANCE_METHOD === $this->getType();
    }

    /**
     * isStaticMethod
     *
     * @return boolean
     */
    public function isStaticMethod()
    {
        return static::C_TYPE_STATIC_METHOD === $this->getType();
    }

    /**
     * isInvokedObject
     *
     * @return boolean
     */
    public function isInvokedObject()
    {
        return static::C_TYPE_INVOKED_OBJECT === $this->getType();
    }

    /**
     * getType
     *
     * @return int
     */
    public function getType()
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
    private function getCallableType(callable $method)
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
    private function getClass()
    {
        if ($this->isMethod() || $this->isInvokedObject()) {
            $parts = $this->explodeCallable($this->handler);

            if ($this->isStaticMethod()) {
                return $parts[0];
            }

            return get_class($parts[0]);
        }

        return '';
    }

    /**
     * getMethod
     *
     * @return string
     */
    private function getMethod()
    {
        if (is_array($this->handler)) {
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

        if (is_string($callable)) {
            return explode('::', $method);
        }

        return [$callable];
    }

    /**
     * doGetReflector
     *
     * @return ReflectionMethod
     */
    private function doGetReflector()
    {
        switch ($this->getType()) {
            case static::C_TYPE_FUNCTION:
            case static::C_TYPE_CLOSURE:
                return new \ReflectionMethod($this->handler);
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
