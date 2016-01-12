<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Tests\Loader;

use Lucid\Resource\Loader\Resolver;
use Lucid\Resource\Exception\LoaderException;

/**
 * @class ResolverTest
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Resource\Loader\ResolverInterface', new Resolver);
    }

    /** @test */
    public function itShouldAddLoaders()
    {
        $res = null;
        $loader = $this->mockLoader();
        $loader->expects($this->once())->method('setResolver');

        $res = new Resolver([$loader]);

        $this->assertSame([$loader], $res->all());
    }

    /** @test */
    public function itShouldResolveResources()
    {
        $res = new Resolver();
        $res->addLoader($loader = $this->mockLoader());

        $loader->method('supports')->with(__FILE__)->willReturn(true);

        $res->resolve(__FILE__);

        $res = new Resolver();
        $res->addLoader($loader = $this->mockLoader());

        $loader->method('supports')->willReturn(false);

        try {
            $res->resolve('nope');
        } catch (LoaderException $e) {
            $this->assertSame('No loader found for resource "nope".', $e->getMessage());
            return;
        }

        $this->fail();
    }

    private function mockLoader()
    {
        return $this->getMockbuilder('Lucid\Resource\Loader\LoaderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
