<?php

/*
 * This File is part of the Lucid\Mux\Cache\Tests\Storage package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Tests\Storage;

/**
 * @trait TestHelper
 *
 * @package Lucid\Mux\Cache\Tests\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait TestHelper
{
    private function mockRoutes()
    {
        return new \Lucid\Mux\Routes;
    }

    private function mockCachedRoutes($routes = null)
    {
        return new \Lucid\Mux\Cache\Routes($routes ?: $this->mockRoutes());
    }
}
