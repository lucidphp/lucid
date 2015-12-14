<?php

/**
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests;

/**
 * @class ProviderTest
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ProviderTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\DI\ProviderInterface', $this->getMock('Lucid\DI\AbstractProvider'));
    }

    /** @test */
    public function itShouldCallConstructorMethodOnCMap()
    {
        $cmap = ['my_service' => 'getServiceMyService'];

        $provider = $this->getMockBuilder('Lucid\DI\AbstractProvider')->setConstructorArgs([$cmap])->getMock();

        $this->assertTrue($provider->provides('my_service'));
        $this->assertFalse($provider->provides('not_my_service'));

        $this->assertNull($provider->provide('my_service'));
    }

    /** @test */
    public function itShouldAutoDetectFactoryMethod()
    {
        $provider = $this->getMock('Lucid\DI\AbstractProvider', ['getServiceMyService']);

        var_dump($provider->provides('my_service'));

        $stub = $this->getMock('myServiceMock');
        $provider->method('getServiceMyService')->willReturn($stub);
        $this->assertTrue($provider->provides('my_service'));
        $this->assertTrue($stub === $provider->provide('my_service'));

    }
}
