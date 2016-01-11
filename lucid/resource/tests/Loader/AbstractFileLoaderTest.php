<?php

namespace Lucid\Resource\Tests\Loader;

use Lucid\Resource\Tests\Stubs\PhpFileLoader;

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

    protected function newLoader($locator = null)
    {
        return $this->getMockBuilder(
            'Lucid\Resource\Loader\AbstractFileLoader',
            ['getExtensions', 'load', 'import', 'supports']
        )
            ->setConstructorArgs([$locator ?: $this->mockLocator()])
            ->getMock();
    }
}
