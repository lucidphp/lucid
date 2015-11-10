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

/**
 * @class HashTestCase
 * @see \PHPUnit_Framework_TestCase
 * @abstract
 *
 * @package Lucid\Hash
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class HashTestCase extends \PHPUnit_Framework_TestCase
{
    protected $hash;

    public function testHashCreateAndValidate()
    {
        $hash = $this->hash->hash('bragging');
        $this->assertTrue($this->hash->check('bragging', $hash));
        $this->assertFalse($this->hash->check('brAgging', $hash));
    }
}
