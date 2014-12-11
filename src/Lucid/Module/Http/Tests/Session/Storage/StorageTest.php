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

/**
 * @class StorageTest
 *
 * @package Lucid\Module\Http\Tests\Session\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class StorageTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function idShouldBeSettable()
    {
        $store = $this->newStore();
        $store->setId($id = 'new_id');

        $this->assertEquals($id, $store->getId());
    }

    /** @test */
    public function nameShouldBeSettable()
    {
        $store = $this->newStore();
        $store->setName($name = 'new_name');

        $this->assertEquals($name, $store->getName());
    }

    /** @test */
    public function itShouldBeInActive()
    {
        $store = $this->newStore();
        $this->assertFalse($store->isActive());
        $this->assertFalse($store->isStarted());
        $this->assertFalse($store->isClosed());
    }

    /** @test */
    public function itShouldBeActiveAfterBeingStarted()
    {
        $store = $this->newStore();
        $store->start();

        $this->assertTrue($store->isActive());
        $this->assertTrue($store->isStarted());
        $this->assertFalse($store->isClosed());
    }

    /** @test */
    public function itShouldBeClosedAfterSaving()
    {
        $store = $this->newStore();
        $store->start();
        $store->save();
        $this->assertTrue($store->isClosed());
    }

    /** @test */
    public function itShouldReturnFalseIfStarted()
    {
        $store = $this->newStore();
        $this->assertTrue($store->start());
        $this->assertFalse($store->start());
    }

    abstract protected function newStore($name = 'TESTSESSION');

    protected function mockHandler()
    {
        return $this->getMock('SessionHandlerInterface');
    }
}
