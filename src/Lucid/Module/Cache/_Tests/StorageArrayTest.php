<?php

/**
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Tests;

use Selene\Module\Cache\Storage;
use Selene\Module\Cache\Driver\ArrayDriver;
use Selene\Module\TestSuite\Traits\TestDrive;

/**
 * @class StorageFilesystemTest
 * @see StorageTestCase
 *
 * @package Selene\Module\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class StorageArrayTest extends StorageTestCase
{

    protected $testDrive;

    use TestDrive;

    protected function setUp()
    {
        parent::setUp();
        $this->cache = new Storage(new ArrayDriver, 'mycache');
    }

    public function testPersistData()
    {
        $this->testDrive = $this->setupTestDrive();
        $file = $this->testDrive.DIRECTORY_SEPARATOR.'storage';
        unset($this->cache);
        $this->cache = new Storage(new ArrayDriver(true, $file), 'mycache');

        $this->cache->set('foo', 'bar');

        unset($this->cache);

        $this->assertFileExists($file);

        $this->cache = new Storage(new ArrayDriver(true, $file), 'mycache');

        $this->assertTrue($this->cache->has('foo'));
        $this->assertEquals('bar', $this->cache->get('foo'));

        unset($this->cache);

    }

    protected function tearDown()
    {
        parent::tearDown();

        if (file_exists($this->testDrive)) {
            $this->teardownTestDrive($this->testDrive);
        }
        $this->testDrive = null;
    }
}
