<?php
/**
 * Created by PhpStorm.
 * User: malcolm
 * Date: 13.10.16
 * Time: 21:19
 */
namespace Lucid\Mux\Handler;


/**
 * @class   Reflector
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author  iwyg <mail@thomas-appel.com>
 */
interface ReflectorInterface
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

    /**
     * @param array ...$args
     *
     * @return mixed
     */
    public function __invoke(...$args);

    /**
     * @return \ReflectionFunctionAbstract
     */
    public function getReflector(): \ReflectionFunctionAbstract;

    /**
     * invokeArgs
     *
     * @param array $args
     *
     * @return mixed
     */
    public function invokeArgs(array $args);

    /**
     * isFunction
     *
     * @return bool
     */
    public function isFunction(): bool;

    /**
     * isClosure
     *
     * @return bool
     */
    public function isClosure(): bool;

    /**
     * isMethod
     *
     * @return bool
     */
    public function isMethod(): bool;

    /**
     * isInstanceMethod
     *
     * @return bool
     */
    public function isInstanceMethod(): bool;

    /**
     * isStaticMethod
     *
     * @return bool
     */
    public function isStaticMethod(): bool;

    /**
     * isInvokedObject
     *
     * @return bool
     */
    public function isInvokedObject(): bool;

    /**
     * getType
     *
     * @return int
     */
    public function getType(): int;
}