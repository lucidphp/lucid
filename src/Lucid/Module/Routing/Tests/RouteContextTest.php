<?php

/*
 * This File is part of the Lucid\Module\Routing\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Tests;

use Lucid\Module\Routing\RouteContext;

/**
 * @class RouteExpressionTest
 *
 * @package Lucid\Module\Routing\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RouteContextTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Lucid\Module\Routing\RouteContextInterface',
            new RouteContext('/', '~/~', [], [])
        );
    }

    /** @test */
    public function itShouldGetIstProperties()
    {
        $exp = new RouteContext('/', '~/~', [], []);

        $this->assertSame('/', $exp->getStaticPath());
        $this->assertSame('~/~', $exp->getRegexp());
        $this->assertSame([], $exp->getTokens());
        $this->assertSame([], $exp->getParameters());
        $this->assertNull($exp->getHostRegexp());
        $this->assertSame([], $exp->getHostParameters());
        $this->assertSame([], $exp->getHostTokens());
    }

    /** @test */
    public function itShouldBeSerializable()
    {
        $exp = new RouteContext('/', '~/~', [], []);

        $s = serialize($exp);
        $us = unserialize($s);

        $this->assertSame('~/~', $us->getRegexp());
    }
}
