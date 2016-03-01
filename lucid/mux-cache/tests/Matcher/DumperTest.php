<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Mux\Cache\Tests\Matcher;

use Lucid\Mux\Cache\Matcher\Dumper;

/**
 * @class DumperTest
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class DumperTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Mux\Cache\Matcher\Dumper', new Dumper);
    }

    /** @test */
    public function itShouldDumpAMapAsString()
    {
        $d = new Dumper;
        $routes = $this->mockRoutes();
        $routes->method('all')->willReturn([]);
        $res = $d->dump($routes);

        $this->assertEquals("<?php\n\nreturn Array();", $res);
    }

    private function mockRoutes()
    {
        return $this->getMockbuilder('Lucid\Mux\RouteCollectionInterface')
            ->disableOriginalConstructor()->getMock();
    }
}
