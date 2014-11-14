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
     * Dispatches a given request.
     *
     * @param RequestContextInterface $request
     *
     * @return mixed returns the result.
     */
    public function dispatch(RequestContextInterface $request);

    /**
     * Get the first route that's been dispatched.
     *
     * @return RouteInterface|null
     */
    public function getFirstRoute();

    /**
     * Get the first route name that's been dispatched.
     *
     * @return string|null
     */
    public function getFirstRouteName();

    /**
     * Get the current dispatched route.
     *
     * @return RouteInterface
     */
    public function getCurrentRoute();

    /**
     * Get the current dispatched route name.
     *
     * @return string
     */
    public function getCurrentRouteName();

    /**
     * Get an UrlGenerator object.
     *
     * @return UrlGeneratorInterface
     */
    public function getGenerator();
}
