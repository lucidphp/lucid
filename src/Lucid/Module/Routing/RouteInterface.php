<?php

/*
 * This File is part of the Lucid\Module\Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Module\Routing;

/**
 * @interface RouteInterface
 *
 * @package Lucid\Module\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouteInterface
{
    /**
     * Get the supported methods
     *
     * @return array a list of supported http methods
     */
    public function getMethods();

    /**
     * Tell if the route supports a given http method.
     *
     * @param string $method
     *
     * @return boolean `TRUE` if the given method is supported, otherwise `FALSE`.
     */
    public function hasMethod($method);

    /**
     * Get the route patter.
     *
     * @return string the route pattern
     */
    public function getPattern();

    /**
     * Get the host
     *
     * @return string the host name, `NULL` if none.
     */
    public function getHost();

    /**
     * Get the default parameters.
     *
     * @return array Array with default route parameters.
     */
    public function getDefaults();

    /**
     * Gets a value from the default parameters
     *
     * @param string $var the parameter name.
     *
     * @return mixed the parameter value.
     */
    public function getDefault($var);

    /**
     * Get parameter constraints.
     *
     * @return array Associative array containing parameter constrains.
     */
    public function getConstraints();

    /**
     * Get a parameter constraint by its parameter name.
     *
     * @return array Associative array containing route constrains.
     *
     * @return string A constraint expression, typically a regular expression.
     */
    public function getConstraint($param);

    /**
     * Get the route handler
     *
     * @return mixed the route handler or its identifyer.
     */
    public function getHandler();

    /**
     * Gets the supported url schemes.
     *
     * @return array a list of supported url schemes
     */
    public function getSchemes();

    /**
     * Tell if a given scheme is supported by this route.
     *
     * @param string $scheme
     *
     * @return boolean `TRUE` if the given scheme is supported, otherwise `FALSE`.
     */
    public function hasScheme($scheme);

    /**
     * Get the route context.
     *
     * If the context doesn't exists, the route will create a new context
     * object.
     * The context object contains infomation about required variables and the
     * regexp matching patter.
     *
     * @see RouteContextInterface
     *
     * @return RouteContextInterface the route context object.
     */
    public function getContext();
}
