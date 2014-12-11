<?php

/*
 * This File is part of the Lucid\Module\Http\Tests\Session\Storage package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Tests\Session\Storage;

use Lucid\Module\Http\Session\Storage\PhpStorage;

/**
 * @class PhpStorageTest
 *
 * @package Lucid\Module\Http\Tests\Session\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpStorageTest extends StorageTest
{
    protected function newStore($name = 'TESTSESSION')
    {
        return new PhpStorage($name, $this->mockHandler());
    }
}
