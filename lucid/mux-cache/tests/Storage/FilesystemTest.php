<?php

namespace Lucid\Mux\Cache\Tests\Storage;

use Lucid\Mux\Cache\Storage\Filesystem;

class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    use TestHelper;

    /** @var string */
    private $path;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Mux\Cache\Storage\Filesystem', new Filesystem($this->path));
    }

    /** @test */
    public function itShouldCheckIfCacheExists()
    {
        $store = new Filesystem($this->path);

        $this->assertFalse($store->exists());
    }

    /** @test */
    public function itShouldWriteToAndReadFromFile()
    {
        $store = new Filesystem($this->path);
        $store->write($this->mockRoutes());

        $this->assertInstanceOf('Lucid\Mux\Cache\CachedCollectionInterface', $store->read());
        $this->assertTrue($store->exists());

    }

    /** @test */
    public function itShouldValidateFile()
    {
        $store = new Filesystem($this->path);
        $store->write($this->mockCachedRoutes());
        $time = time();

        $this->assertEquals($time, $store->getLastWriteTime());

        $this->assertTrue($store->isValid($time));
        $this->assertFalse($store->isValid($time - 100));
    }

    protected function setUp()
    {
        $this->setupTestDir();
    }

    protected function tearDown()
    {
        if (!is_dir($this->path)) {
            return;
        }

        foreach (glob($this->path.DIRECTORY_SEPARATOR.'*') as $file) {
            unlink($file);
        }

        rmdir($this->path);
    }

    private function setupTestDir()
    {
        $this->path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . strtr(__CLASS__, ['\\' => '_']);
    }
}
