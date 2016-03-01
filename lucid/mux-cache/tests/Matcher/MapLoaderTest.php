<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Tests\Matcher;

use Lucid\Mux\Cache\Matcher\MapLoader;

/**
 * @class MapLoaderTest
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MapLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $path;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf(
            'Lucid\Mux\Cache\Matcher\MapLoader',
            new MapLoader($this->mockDumper(), 'fakepath')
        );
    }

    /** @test */
    public function itShouldCreateAnLoadMap()
    {
        $loader = new MapLoader($dumper = $this->mockDumper(), $this->path);
        $routes = $this->mockRoutes();

        $ret = ['foo' => 'bar'];
        $routes->expects($this->exactly(3))->method('getTimestamp')->willReturn(time() - (3600 * 24));
        $dumper->expects($this->once())->method('dump')->with($routes)
            ->willReturn('<?php return '.var_export($ret, true).';');

        $this->assertSame($ret, $loader->load($routes), 'Should\'ve created and loaded.');
        // load from existing file.
        $this->assertSame($ret, $loader->load($routes), 'Should\'ve loaded from existing file.');
    }

    /** @test */
    public function itShouldLoadExistingMap()
    {
        $time = time() - 100;
        $file = $this->path . DIRECTORY_SEPARATOR . MapLoader::DEFAULT_PREFIX.hash('sha1', $time).'.php';
        $ret = ['foo' => 'bar'];
        file_put_contents($file, '<?php return '.var_export($ret, true).';');

        $loader = new MapLoader($dumper = $this->mockDumper(), $this->path);
        $routes = $this->mockRoutes();

        $routes->expects($this->exactly(2))->method('getTimestamp')->willReturn($time);
        $dumper->expects($this->exactly(0))->method('dump');

        $this->assertSame($ret, $loader->load($routes), 'Should\'ve loaded from existing file.');
    }

    protected function setUp()
    {
        $this->path = $this->getDumpPath();

        if (is_dir($this->path)) {
            return;
        }

        mkdir($this->path, 0775, true);
    }

    protected function tearDown()
    {
        foreach ($this->glob() as $file) {
            unlink($file);
        }

        rmdir($this->path);
    }

    private function glob()
    {
        foreach (glob($this->path . DIRECTORY_SEPARATOR . '*.php') as $file) {
            yield($file);
        }
    }

    private function mockRoutes()
    {
        return $this->getMockbuilder('Lucid\Mux\Cache\CachedCollectionInterface')
            ->disableOriginalConstructor()->getMock();
    }

    private function mockDumper()
    {
        return $this->getMockbuilder('Lucid\Mux\Cache\Matcher\Dumper')
            ->disableOriginalConstructor()->getMock();
    }

    private function getDumpPath()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . strtolower(strtr(__CLASS__, ['\\' => '_']));
    }
}
