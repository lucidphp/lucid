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

use Lucid\Resource\Loader\ChainedLoader;

/**
 * @class ChainedLoaderTest
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ChainedLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Resource\Loader\LoaderInterface', new ChainedLoader($this->mockResolver()));
    }

    /** @test */
    public function itShouldGetResolver()
    {
        $loader = new ChainedLoader($res = $this->mockResolver());
        $this->assertTrue($res === $loader->getResolver());
    }

    /** @test */
    public function itShouldOnlyResolveLoadersFromResolver()
    {
        $loader = new ChainedLoader($this->mockResolver());
        $ld = $this->mockLoader();
        $ld->expects($this->once())->method('supports');

        $loader->setResolver($res = $this->mockResolver([$ld]));
        $this->assertTrue($res === $loader->getResolver());

        $loader->supports('resource');
    }

    /** @test */
    public function itShouldNotSupportResource()
    {
        $loader = new ChainedLoader($this->mockResolver());

        $this->assertFalse($loader->supports('resource'));
    }

    /** @test */
    public function itShouldResolveLoaderAndCallSupportOnLoaders()
    {
        $loaderA = $this->mockLoader();
        $loaderB = $this->mockLoader();

        $type = 'php';

        $loaderA->expects($this->any())->method('supports')->with($type)->willReturn(false);
        $loaderB->expects($this->any())->method('supports')->with($type)->willReturn(true);

        $loader = new ChainedLoader($this->mockResolver([$loaderA, $loaderB]));

        $this->assertTrue($loader->supports($type));

        $this->assertTrue($loaderB == $loader->resolve($type));
    }

    /** @test */
    public function itShouldReturnFirstSupportedLoader()
    {
        $loaderA = $this->mockLoader();
        $loaderB = $this->mockLoader();

        $type = 'php';

        $loaderA->expects($this->once())->method('supports')->with($type)->willReturn(true);
        $loaderB->method('supports')->with($type)->willReturnCallback(function () {
            $this->fail('Loader should not be called.');
            return false;
        });

        $loader = new ChainedLoader($this->mockResolver([$loaderA, $loaderB]));

        $this->assertTrue($loader->supports($type));
    }

    /** @test */
    public function itShouldCallLoadOnLoaderWhenImportingResources()
    {

        $loaderA = $this->mockLoader();
        $loaderB = $this->mockLoader();

        $type = 'file';

        $loaderA->expects($this->any())->method('supports')->with($type)->willReturn(true);
        $loaderB->expects($this->any())->method('supports')->with($type)->willReturn(false);

        $loaderA->expects($this->exactly(2))->method('load')->with($type);

        $loader = new ChainedLoader($this->mockResolver([$loaderA, $loaderB]));

        $loader->import($type);
        $loader->load($type);
    }

    /** @test */
    public function itShouldThrowLoaderExceptionIfLoaderIsNotResolveable()
    {
        $loader = new ChainedLoader($this->mockResolver());

        try {
            $loader->load('resource');
        } catch (\Lucid\Resource\Exception\LoaderException $e) {
            $this->assertEquals('No matching loader found.', $e->getMessage());
        }

    }

    /** @test */
    public function itShouldAddAndRemoveListener()
    {
        $ld = $this->mockLoader();
        $res = $this->mockResolver([$ld]);

        $listener = $this->getMockbuilder('Lucid\Resource\Loader\ListenerInterface')
            ->disableOriginalConstructor()->getMock();

        $ld->expects($this->once())->method('addListener')->with($listener);
        $ld->expects($this->once())->method('removeListener')->with($listener);

        $loader = new ChainedLoader($res);

        $loader->addListener($listener);
        $loader->removeListener($listener);
    }

    private function mockLoader()
    {
        return $this->getMockbuilder('Lucid\Resource\Loader\LoaderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function mockResolver(array $loaders = [])
    {
        $resolver = $this->getMockbuilder('Lucid\Resource\Loader\ResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $resolver->method('all')->willReturn($loaders);

        return $resolver;
    }
}
