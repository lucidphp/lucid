<?php

/*
 * This File is part of the Lucid\DI\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests;

use Lucid\DI\Container;
use Lucid\DI\Tests\Stubs\MethodContainer;

/**
 * @class FactoryContainerTest
 *
 * @package Lucid\DI\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MethodAwareContainerTest extends ContainerTest
{
    /** @test */
    public function itShouldFindAServiceByMethod()
    {
        $container = $this->newContainer($provider = $this->mockProvider(['getServiceMyService']));

        $provider->expects($this->exactly(2))->method('getServiceMyService')->willReturn(new \stdClass);

        $this->assertTrue($container->has('my_service'));
        $container->get('my_service');
        $this->assertInstanceof('stdClass', $container->get('my_service'));
    }

    protected function newContainer($provider = null)
    {
        return new Container($provider);
    }

    private function mockProvider(array $methods)
    {
        return $this
            ->getMockBuilder('Lucid\DI\AbstractProvider')
            ->setMethods($methods)
            ->getMock();
    }
}
