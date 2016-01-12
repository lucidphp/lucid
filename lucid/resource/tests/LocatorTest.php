<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Tests;

use Lucid\Resource\Locator;

/**
 * @class LocatorTest
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class LocatorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Resource\LocatorInterface', new Locator);
    }

    /** @test */
    public function itShouldLocateFiles()
    {
        $paths = [__DIR__];
        $file = basename(__FILE__);

        $locator = new Locator($paths);

        $this->assertInstanceOf('Lucid\Resource\CollectionInterface', $res = $locator->locate($file));

        $resources = $res->all();
        $this->assertSame(__FILE__, (string)$resources[0]);
    }

    /** @test */
    public function itShouldLocateMultipleFiles()
    {
        $base = dirname(__FILE__).DIRECTORY_SEPARATOR.'Fixures'.DIRECTORY_SEPARATOR.'loc_';

        $locator = new Locator([$baseA = $base.'a', $baseB = $base.'b']);

        $res = $locator->locate('file.txt', true);

        $this->assertArrayHasKey(0, $res->all());
        $this->assertArrayHasKey(1, $res->all());

        $res = $res->all();

        $this->assertSame($baseA.DIRECTORY_SEPARATOR.'file.txt', (string)$res[0]);
        $this->assertSame($baseB.DIRECTORY_SEPARATOR.'file.txt', (string)$res[1]);
    }

    /** @test */
    public function itShouldAddPathsToPatharray()
    {
        $base = dirname(__FILE__).DIRECTORY_SEPARATOR.'Fixures'.DIRECTORY_SEPARATOR.'loc_';

        $locator = new Locator([$baseA = $base.'a']);

        $locator->addPaths([$baseB = $base.'b', $base.'a']);
        $res = $locator->locate('file.txt', true);

        $this->assertArrayHasKey(0, $res->all());
        $this->assertArrayHasKey(1, $res->all());

        $res = $res->all();

        $this->assertSame($baseA.DIRECTORY_SEPARATOR.'file.txt', (string)$res[0]);
        $this->assertSame($baseB.DIRECTORY_SEPARATOR.'file.txt', (string)$res[1]);
    }

    /** @test */
    public function itShouldReturnEmptyCollectionIfPathIsNotADirectoryOrFileDoesNotExist()
    {
        $base = dirname(__FILE__).DIRECTORY_SEPARATOR.'Fixures'.DIRECTORY_SEPARATOR.'loc_';
        $locator = new Locator(['/not/a/path', $base.'a']);

        $this->assertSame([], $locator->locate('foo')->all());
    }

    /** @test */
    public function itShouldThrowIfRootpathIsInvalid()
    {
        $locator = new Locator(['bar/foo']);
        $locator->setRootPath('/not/a/path');
        try {
            $locator->locate('text.txt');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Foo');
    }

    /** @test */
    public function itShouldExpandRelativePaths()
    {
        $locator = new Locator;
        $locator->setRootPath(__DIR__.DIRECTORY_SEPARATOR.'Fixures');
        $locator->addPath('loc_a');

        $res = $locator->locate('file.txt');

        $this->assertArrayHasKey(0, $res->all());
    }

    /** @test */
    public function itShouldReturnCollection()
    {
        $paths = [__DIR__];
        $file = basename(__FILE__);

        $locator = new Locator($paths);

        $this->assertInstanceOf('Lucid\Resource\CollectionInterface', $locator->locate($file, true));
    }
}
