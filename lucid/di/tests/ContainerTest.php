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

use Lucid\DI\Container;
use Interop\Container\Exception\NotFoundException as InteropNotFoundException;

/**
 * @class ContainerTest
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerTest extends AbstractContainerTest
{
    /** @test */
    public function itShouldThrowNotFoundExceptionIfNoProviderAndNoIdIsSet()
    {
        $container = new Container();

        try {
            $container->get('foo');
        } catch (InteropNotFoundException $e) {
            $this->assertEquals('Service "foo" could not be found on this container.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldThrowNotFoundExceptionIfProviderButNoId()
    {
        $container = new Container($this->getMock('Lucid\DI\AbstractProvider'));

        try {
            $container->get('bar');
        } catch (InteropNotFoundException $e) {
            $this->assertEquals('Service "bar" could not be found on this container.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldCallProvideMethodOnProvider()
    {
        $called = false;
        $provider  = $this->getMock('Lucid\DI\AbstractProvider', ['getServiceFoo']);
        $provider->method('getServiceFoo')->willReturnCallback(function ($id) use (&$called) {
            $called = true;
        });

        $container = new Container($provider);

        $this->assertNull($container->get('foo'));
        $this->assertTrue($called, '"getServiceFoo" should habe been called.');
    }

    /**
     * {@inheritdoc}
     */
    protected function newContainer()
    {
        return new Container;
    }
}
