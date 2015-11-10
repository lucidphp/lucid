<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Tests;

use Lucid\Mux\Route;
use Lucid\Mux\Routes;
use Lucid\Mux\Cache\Routes as CachedRouteCollection;

/**
 * @class CachedRouteCollectionTest
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CachedRouteCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $routes = new CachedRouteCollection(new Routes);

    }

    /** @test */
    public function itIsExpectedThat()
    {
        $c = new Routes;

        foreach (range(1, 100) as $r) {
            if (0 === $r % 2) {
                $c->add('route_'.$r, new Route('/route/'.$r, 'action_'.$r, 'GET', null, [], [], 'https'));
            } else {
                $c->add('route_'.$r, new Route('/route/'.$r, 'action_'.$r, 'POST'));
            }
        }

        $cached = new CachedRouteCollection($c);

        $s1 = microtime(true);
        $cached->findByScheme('https');
        $ss1 = microtime(true);
        $s2 = microtime(true);
        $c->findByScheme('https');
        $ss2 = microtime(true);

        $this->assertTrue(($ss1 - $s1) < ($ss2 - $s2));
        $this->assertTrue(in_array('route_12', array_keys($cached->findByMethod('GET')->all())));
    }
}
