<?php

namespace Lucid\Cache\Tests\Client;

use Lucid\Cache\Client\Filesystem;

class FilesystemTest extends AbstractClientTest
{
    /** @var string */
    private $path;

    /** @test */
    public function flushingCacheShouldReturnBoolean()
    {
        $this->newClient()->write('item.exists', 'exists', time() + 2000);

        $driver = $this->newClient();
        $this->assertTrue($driver->flush());
    }

    /** @test */
    public function itShouldReturnTrueIfItemExists()
    {
        $this->newClient()->write('item.exists', 'exists', time() + 2000);
        parent::itShouldReturnTrueIfItemExists();
    }

    /** @test */
    public function itShouldFetchStoredItems()
    {
        $this->newClient()->write('item.exists', 'exists', time() + 2000);
        parent::itShouldFetchStoredItems();
    }

    /**
     * @test
     * @dataProvider TimeProvider
     */
    public function itShouldReturnBooleanWhenStoringItems($time)
    {
        $driver = $this->newClient();

        $this->assertTrue($driver->write('item.success', 'data', $time));
        //$this->assertFalse($driver->write('item.fails', 'data', $time));
    }

    /** @test */
    public function itShouldReturnBooleanWhenDeletingItems()
    {
        $driver = $this->newClient();
        $driver->write('item.exists', 'data', time() + 2000);

        $this->assertFalse($driver->delete('item.fails'));
        $this->assertTrue($driver->delete('item.exists'));
    }

    /** @test */
    public function itShouldReturnIncrementedValue()
    {
        $driver = $this->newClient();
        $driver->write('item.inc', 1, time() + 2000);

        parent::itShouldReturnIncrementedValue();
    }

    /** @test */
    public function itShouldReturnDecrementedValue()
    {
        $driver = $this->newClient();
        $driver->write('item.dec', 1, time() + 2000);

        parent::itShouldReturnDecrementedValue();
    }

    public function timeProvider()
    {
        return [
            [60]
        ];
    }

    protected function newClient()
    {
        return new Filesystem($this->getCachePath());
    }

    protected function tearDown()
    {
        if (null !== $this->path) {
            $this->rmDir($this->path);
            $this->path = null;
        }

        parent::tearDown();
    }

    private function rmDir($cpath)
    {
        $itr = new \FilesystemIterator($cpath, \FilesystemIterator::SKIP_DOTS);

        foreach ($itr as $path => $item) {

            if ($item->isDir()) {
                $this->rmDir($path);
            }

            if ($item->isFile()) {
                unlink($path);
            }
        }

        rmdir($cpath);
    }

    private function getCachePath()
    {
        if (null === $this->path) {
            $this->path = sys_get_temp_dir() . '/fscachetest' . time();
        }

        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }

        return $this->path;
    }
}
