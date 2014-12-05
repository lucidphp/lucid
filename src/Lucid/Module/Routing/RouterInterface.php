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

use Lucid\Module\Routing\Http\UrlGeneratorInterface;
use Lucid\Module\Routing\Http\RequestContextInterface;

/**
 * @interface RouterInterface
 *
 * @package Lucid\Module\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouterInterface extends RouteDispatcherInterface
{
    /**
     * Delegates a given request context to a handler.
     *
     * @param RequestContextInterface $request the request context.
     *
     * @return mixed returns the result of the handler.
     */
    public function dispatch(RequestContextInterface $request);

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
