<?php

/**
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests\Resolver;

use Lucid\DI\Definition\Service;
use Lucid\DI\Definition\Factory;
use Lucid\DI\Reference\Caller as CallerReference;
use Lucid\DI\Reference\Service as ServiceReference;
use Lucid\DI\Resolver\ReflectionResolver;
use Lucid\DI\Exception\ResolverException;

/**
 * @class ReflectionResolverTest
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ReflectionResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldThrowIfNotInstantiable()
    {
        $map = [
            ['foo', $foo = $this->mockService()],
            ['bar', $bar = $this->mockService()],
        ];

        $container = $this->mockContainer();
        $container->method('getService')->will($this->returnValueMap($map));

        $foo->method('isBlueprint')->willReturn(true);
        $foo->method('isInjected')->willReturn(false);

        $bar->method('isInjected')->willReturn(true);
        $bar->method('isBlueprint')->willReturn(false);

        $resolver = new ReflectionResolver;

        try {
            $resolver->resolve('foo', $container);
        } catch (ResolverException $e) {
            $this->assertEquals('The requested service "foo" is not instantiable.', $e->getMessage());
        }

        try {
            $resolver->resolve('bar', $container);
        } catch (ResolverException $e) {
            $this->assertEquals('The requested service "bar" is not instantiable.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    private function mockContainer()
    {
        return $this->getMockbuilder('Lucid\DI\ContainerBuilderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockService()
    {
        return $this->getMockbuilder('Lucid\DI\Definition\ServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
