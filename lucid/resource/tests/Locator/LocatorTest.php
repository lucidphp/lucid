<?php

namespace Lucid\Resource\Tests\Locator;

use Lucid\Resource\Locator\Locator;

/**
 * @class LocatorTest
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Lucid\Resource\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class LocatorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Resource\Locator\LocatorInterface', new Locator([]));
    }

    /** @test */
    public function itShouldLocateFiles()
    {
        $paths = [__DIR__];
        $file = basename(__FILE__);

        $locator = new Locator($paths);

        $this->assertSame(__FILE__, $locator->locate($file));
    }

    /** @test */
    public function itShouldCollectPathsAsArray()
    {
        $paths = [__DIR__];
        $file = basename(__FILE__);

        $locator = new Locator($paths);

        $this->assertSame([__FILE__], $locator->locate($file, true));
    }
}
