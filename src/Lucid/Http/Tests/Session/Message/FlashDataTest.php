<?php

/*
 * This File is part of the Lucid\Http\Tests\Session\Data package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Tests\Session\Message;

use Lucid\Http\Session\Message\Flashes;

/**
 * @class FlashDataTest
 *
 * @package Lucid\Http\Tests\Session\Data
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FlashDataTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $flashes = new Flashes;
    }

    /** @test */
    public function flushingShouldDeleteValuesInPool()
    {
        $flashes = new Flashes;
        $data = ['a' => 'b', 'c' => 'd'];

        $flashes->initialize($data);

        $this->assertTrue($flashes->has('a'));
        $flashes->flush('a');
        $this->assertFalse($flashes->has('a'));
    }

    /** @test */
    public function flushingAllShouldEmptyPool()
    {
        $flashes = new Flashes;
        $data = ['a' => 'b', 'c' => 'd'];
        $flashes->initialize($data);

        $this->assertSame($data, $flashes->flushAll());

        $this->assertEmpty($flashes->all());
    }
}
