<?php

/*
 * This File is part of the Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing;

/**
 * @class RouteInterface
 *
 * @package Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouteInterface
{
    /**
     * getMethod
     *
     * @return string
     */
    public function getMethods();

    /**
     * getPattern
     *
     * @return string
     */
    public function getPattern();

    /**
     * getHost
     *
     * @return string|null
     */
    public function getHost();

    /**
     * getDefaults
     *
     *
     * @return array
     */
    public function getDefaults();

    /**
     * getDefault
     *
     * @param string $var
     *
     * @return mixed|string
     */
    public function getDefault($var);

    /**
     * getConstraints
     *
     * @return void
     */
    public function getConstraints();

    /**
     * getConstraint
     *
     * @param mixed $param
     *
     * @return void
     */
    public function getConstraint($param);

    /**
     * getHandler
     *
     * @return void
     */
    public function getHandler();

    /**
     * getExpression
     *
     * @return RouteExpressionInterface
     */
    public function getContext();
}
