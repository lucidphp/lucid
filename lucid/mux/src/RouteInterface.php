<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux;

use Serializable;

/**
 * @interface RouteInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouteInterface extends Serializable
{
    /** @var string */
    const DEFAULT_METHODS = 'GET';

    /** @var string */
    const DEFAULT_SCHEMES = 'http|https';

    /**
     * Lists the supported methods.
     *
     * @return array a list of supported http methods
     */
    public function getMethods() : array;

    /**
     * Tell if the route supports a given http method.
     *
     * @param string $method a valid http method such as GET, POST, DELETE, etc.
     *
     * @return bool `true` if the given method is supported, otherwise `false`.
     */
    public function hasMethod(string $method) : bool;

    /**
     * Get the route patter.
     *
     * A route pattern represents the path of the route and can contain various
     * placeholder marks enclosed by curly braces such as  `/user/{id}` or `/user/{id?}`.
     *
     * @return string the route pattern
     */
    public function getPattern() : string;

    /**
     * Get the host
     *
     * @return string the host name, `NULL` if none.
     */
    public function getHost() : ?string;

    /**
     * Get the default parameters.
     *
     * @return array Array with default route parameters.
     */
    public function getDefaults() : array;

    /**
     * Gets a value from the default parameters
     *
     * @param string $var the parameter name.
     *
     * @return mixed the parameter value.
     */
    public function getDefault(string $var);

    /**
     * Get parameter constraints.
     *
     * @return array Associative array containing parameter constrains.
     */
    public function getConstraints() : array;

    /**
     * Get a parameter constraint by its parameter name.
     *
     * @param string $param
     * @return string A constraint expression, typically a regular expression.
     */
    public function getConstraint(string $param) : string;

    /**
     * Get the route handler
     *
     * @return mixed the route handler or its identifier.
     */
    public function getHandler() /*callable | string */;

    /**
     * Gets the supported url schemes.
     *
     * @return array a list of supported url schemes
     */
    public function getSchemes() : array;

    /**
     * Tell if a given scheme is supported by this route.
     *
     * @param string $scheme
     *
     * @return bool `TRUE` if the given scheme is supported, otherwise `FALSE`.
     */
    public function hasScheme(string $scheme) : bool;

    /**
     * Get the route context.
     *
     * If the context doesn't exists, the route will create a new context
     * object.
     * The context object contains information about required variables and the
     * regexp matching patter.
     *
     * @see RouteContextInterface
     *
     * @return RouteContextInterface the route context object.
     */
    public function getContext() : RouteContextInterface;
}
