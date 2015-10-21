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

/**
 * @interface MultiplexerInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface MultiplexerInterface
{
    /**
     * dispatch
     *
     * @param ContextInterface $context
     *
     * @return void
     */
    public function dispatch(ContextInterface $context);

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
     * Get an UrlGenerator object.
     *
     * @return UrlGeneratorInterface an implementation of UrlGeneratorInterface.
     */
    public function getGenerator();
}
