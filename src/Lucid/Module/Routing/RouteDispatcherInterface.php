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
 * @interface RouteDispatcherInterface
 *
 * @package Lucid\Module\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouteDispatcherInterface
{
    /**
     * Delegates a route name to a handler.
     *
     * @param string $name the route name to dispatch.
     * @param array  $parameters parameters required by the route.
     * @param array  $options route options as of `$base_path`, `$method`,
     * `$query`, `$scheme`, `$scheme`, `$host`, and `$port`.
     *
     * @return void
     */
    public function dispatchRoute($name, array $parameters = [], array $options = []);
}
