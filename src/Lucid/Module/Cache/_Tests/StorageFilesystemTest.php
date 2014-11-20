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
use Selene\Module\Filesystem\Filesystem;
use Selene\Module\Cache\Driver\FilesystemDriver;
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
class StorageFilesystemTest extends StorageTestCase
{
    use TestDrive;

    protected $testDrive;

    protected function setUp()
    {
        parent::setUp();
        $this->testDrive = $this->setupTestDrive();
        $this->cache = new Storage(new FilesystemDriver(new Filesystem, $this->testDrive), 'mycache');
    }

    /**
     * tearDown
     *
     *
     * @access protected
     * @return mixed
     */
    protected function tearDown()
    {
        parent::tearDown();

        if (file_exists($this->testDrive)) {
            $this->teardownTestDrive($this->testDrive);
        }
        $this->testDrive = null;
    }
}
