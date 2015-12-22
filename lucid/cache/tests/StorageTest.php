<?php

/*
 * This File is part of the Lucid\Cache\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Tests;

use Lucid\Cache\Storage;
use Lucid\Cache\Client\InMemory;

/**
 * @class CacheTest
 *
 * @package Lucid\Cache\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Lucid\Cache\CacheInterface',
            $cache = $this->newCache()
        );

        $this->assertInstanceof(
            'Lucid\Cache\SectionableInterface',
            $cache
        );
    }

    /** @test */
    public function itShouldCheckForItemsToExist()
    {
        $cache = $this->newCache();

        $cache->set('item.exists', 'item');

        $this->assertFalse($cache->has('item.fails'));
        $this->assertTrue($cache->has('item.exists'));
    }

    /** @test */
    public function itShouldRetreiveItemsFromCache()
    {
        $cache = $this->newCache();

        $cache->set('item.exists', 'item');

        $this->assertNull($cache->get('item.fails'));
        $this->assertSame('item', $cache->get('item.exists'));
    }

    /** @test */
    public function itShouldRetreiveDefaultValues()
    {
        $this->assertSame('item', $this->newCache()->get('item.fails', 'item'));
    }

    /** @test */
    public function itShouldBeSectionAble()
    {
        $cache = $this->newCache();

        $this->assertInstanceof(
            'Lucid\Cache\SectionableInterface',
            $sec = $cache->section('sect')
        );

        $this->assertFalse($cache === $sec);
    }

    protected function newCache()
    {
        return new Storage(new InMemory(false));
    }
}
