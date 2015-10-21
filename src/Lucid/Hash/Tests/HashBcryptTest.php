<?php

/*
 * This File is part of the Lucid\Hash package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Hash\Tests;

use Lucid\Hash\HashBcrypt;

/**
 * @class HashBcryptTest
 * @see HashTestCase
 *
 * @package Lucid\Hash
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class HashBcryptTest extends HashTestCase
{
    protected function setUp()
    {
        $this->hash = new HashBcrypt();
    }
}
