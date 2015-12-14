<?php

namespace Lucid\Config\Tests\Loader;

use Lucid\Config\Loader\PhpLoader;

class PhpLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Resource\Loader\LoaderInterface', new PhpLoader);
    }
}
