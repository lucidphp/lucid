<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux;

use Lucid\Mux\Request\ContextInterface as RequestContext;

/**
 * @interface MultiplexerInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouterInterface
{
    /**
     * dispatch
     *
     * @param ContextInterface $context
     *
     * @return void
     */
    public function dispatch(RequestContext $context);

    /**
     * route
     *
     * @param RouteInterface $route
     *
     * @return void
     */
    public function route($name, array $parameters = [], array $options = []);

    /**
     * Get the first route that's been dispatched.
     *
     * @return RouteInterface the route object, `NULL` if none.
     */
    public function getFirstRoute();

    /**
     * Get the first route name that's been dispatched.
     *
     * @return string the route name, `NULL` if none.
     */
    public function getFirstRouteName();

    /**
     * Get the current dispatched route.
     *
     * @return RouteInterface a route object.
     */
    public function getCurrentRoute();

    /**
     * Get the current dispatched route name.
     *
     * @return string the route name.
     */
    public function getCurrentRouteName();

    /**
     * Generates a http url for a given route name.
     *
     * @param string $name
     * @param string $host
     * @param array $vars
     * @param bool $rel
     */
    public function getUrl($name, $host = null, array $vars = [], $rel = true);
}
