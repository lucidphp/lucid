<?php

namespace Lucid\Package\Tests;

use Lucid\Package\Tests\Stubs\Provider as ProviderStub;

class AbstractProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Package\ProviderInterface', $this->mockProvider());
    }

    /** @test */
    public function itShouldGetPackagePath()
    {
        $expectedPath = __DIR__.DIRECTORY_SEPARATOR.'Stubs';

        $provider = $this->getProviderStub();

        $this->assertSame($expectedPath, $provider->getPath());
    }

    /** @test */
    public function itShouldGetPackageName()
    {
        $provider = $this->getProviderStub();
        $this->assertEquals('Provider', $provider->getName());
    }

    /** @test */
    public function itShouldGetPackageAlias()
    {
        $provider = $this->getProviderStub();
        $this->assertEquals('provider', $provider->getAlias());

        $provider = $this->mockProvider(['getName', 'getPostFix']);

        $provider->method('getName')->willReturn('SomeFancyPackageProvider');
        $provider->method('getPostFix')->willReturn('provider');

        $this->assertEquals('some_fancy_package', $provider->getAlias());
    }

    private function getProviderStub()
    {
        return new ProviderStub;
    }

    private function mockProvider(array $methods = null)
    {
        if (null === $methods) {
            return $this->getMock('Lucid\Package\AbstractProvider');
        }

        return $this->getMock('Lucid\Package\AbstractProvider', $methods);
    }
}
