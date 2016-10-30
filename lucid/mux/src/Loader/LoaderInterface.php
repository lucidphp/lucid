<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Loader;

use Lucid\Resource\Loader\LoaderInterface as BaseLoaderInterface;

/**
 * @interface LoaderInterface
 *
 * @package Lucid\Mux\Loader
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface LoaderInterface extends BaseLoaderInterface
{
    /**
     * Load routes
     *
     * @param mixed $routes
     *
     * @return Lucid\Mux\RouteContextInterface
     */
    public function loadRoutes($routes);
}
