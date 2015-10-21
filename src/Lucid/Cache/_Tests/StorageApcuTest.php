<?php

/**
 * This File is part of the Selene\Module\Cache\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Tests;

use Selene\Module\Cache\Storage;
use Selene\Module\Cache\Driver\ApcuDriver;

class StorageApcuTest extends StorageApcTest
{
    protected function setUp()
    {
        // if in hhvm or if cli usage is not set, skip tests
        if (false !== strrpos(PHP_VERSION, 'hiphop') || !ini_get('apc.enable_cli')) {
            $this->markTestSkipped('Environment doesn\'t support APC');
        }

        // Skip if APCu is not available.
        if (!extension_loaded('apcu')) {
            $this->markTestSkipped('No APCu extension loaded.');
        }

        $this->cache = new Storage(new ApcuDriver);
    }
}
