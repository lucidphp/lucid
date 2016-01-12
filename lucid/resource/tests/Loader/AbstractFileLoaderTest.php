<?php

namespace Lucid\Resource\Tests\Loader;

use Lucid\Resource\Tests\Stubs\PhpFileLoader;
use Lucid\Resource\Loader\AbstractFileLoader;
use Lucid\Resource\Exception\LoaderException;

class AbstractFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Resource\Loader\LoaderInterface', $this->newLoader());
    }

    /** @test */
    public function itShouldSupportGivenFiles()
    {
        $loader = $this->newLoader();
        $loader->method('getExtensions')->willReturn(['php']);

        $this->assertTrue($loader->supports(__FILE__));
    }

    /** @test */
    public function itShouldAddAndGetResolver()
    {
        $loader = $this->newLoader();
        $res = $this->mockResolver();
        $loader->setResolver($res);

        $this->assertTrue($res === $loader->getResolver());
    }

    /** @test */
    public function itShouldCallResolverOnImport()
    {
        $loader = $this->newLoader(null, ['getResolver']);
        $loader->method('getExtensions')->willReturn([]);
        $loader->expects($this->once())->method('getResolver')->willReturn($res = $this->mockResolver());
        $res->expects($this->once())->method('resolve')->with('resource')->willReturn($ld = $this->mockLoader());
        $ld->expects($this->once())->method('load')->with('resource');

        $loader->import('resource');
    }

    /** @test */
    public function itShouldThrowOnImportIfLoaderIsNotResolved()
    {
        $loader = $this->newLoader(null, ['getResolver']);
        $loader->method('getExtensions')->willReturn([]);
        $loader->expects($this->once())->method('getResolver')->willReturn($res = $this->mockResolver());
        $exc = null;
        $res->expects($this->once())->method('resolve')->with('resource')->willReturnCallback(function () use (&$exc) {
            $exc = new LoaderException;
            throw $exc;
        });

        try {
            $loader->import('resource');
        } catch (LoaderException $e) {
            $this->assertSame($exc, $e->getPrevious());
        }
    }

    /** @test */
    public function itShouldThrowLoaderExceptionIfResolverIsMissing()
    {
        $loader = $this->newLoader(null, ['getResolver']);
        $loader->method('getExtensions')->willReturn([]);
        $loader->expects($this->once())->method('getResolver')->willReturn(null);

        try {
            $loader->import('resource');
        } catch (LoaderException $e) {
            $this->assertSame('No loader found for resource "resource".', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail();
        }
    }

    /** @test */
    public function itShouldCallLoadOnImport()
    {
        $loader = $this->newLoader();
        $loader->method('getExtensions')->willReturn(['txt']);
        $loader->expects($this->once())->method('load');

        $loader->import('resource.txt');
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $loader = new PhpFileLoader($locator = $this->mockLocator());

        $called = false;
        $locator->method('locate')->with(basename(__FILE__))->willReturnCallBack(function () use (&$called) {
            $called = true;
            $c = $this->mockCollection();
            $c->method('all')->willReturn([]);

            return $c;
        });

        $loader->load(basename(__FILE__));

        $this->assertTrue($called);
    }

    protected function mockCollection()
    {
        return $this->getMockbuilder('Lucid\Resource\CollectionInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockLocator()
    {
        return $this->getMockbuilder('Lucid\Resource\LocatorInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockResolver()
    {
        return $this->getMockbuilder('Lucid\Resource\Loader\ResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function mockLoader()
    {
        return $this->getMockbuilder('Lucid\Resource\Loader\LoaderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function newLoader($locator = null, array $methods = [])
    {
        return $this->getMockBuilder(AbstractFileLoader::class)
            ->setMethods(array_merge(['getExtensions', 'load', 'supports', 'doLoad'], $methods))
            ->setConstructorArgs([$locator ?: $this->mockLocator()])
            ->getMock();
    }
}
