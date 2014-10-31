<?php

/*
 * This File is part of the Lucid\Module\Routing\Loader package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Loader;

use Lucid\Module\Resource\Loader\AbstractLoader;

/**
 * @class RouteCollectionLoader
 *
 * @package Lucid\Module\Routing\Loader
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class RouteCollectionLoader extends AbstractLoader
{
    public function __construct(ResourceLocatorInterface $locator, RouteCollectionInterface $routes = null)
    {
        $this->routes  = $routes;
        $this->locator = $locator;
    }

    public function load($resource)
    {

    }
}
