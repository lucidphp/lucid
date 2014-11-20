<?php

/*
 * This File is part of the Lucid\Module\Cache\Tests\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Cache\Tests\Driver;

use Lucid\Module\Cache\Driver\ApcDriver;

/**
 * @class ApcDriverTest
 *
 * @package Lucid\Module\Cache\Tests\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ApcDriverTest extends DriverTest
{
    /** @test */
    public function itShouldParseMinutesToSeconts()
    {
        $driver = $this->newDriver();

        $this->assertSame(60, $driver->parseExpireTime(1));
    }

    /** @test */
    public function itShouldParseDateToSeconds()
    {
        $driver = $this->newDriver();

        $this->assertSame(60, $driver->parseExpireTime('60 seconds'));
    }

    /** @test */
    public function storingItemsShouldReturnBoolean()
    {
        $driver = $this->newDriver();

        $this->assertTrue($driver->saveForever('item.exists', 'data'));
        $this->assertFalse($driver->saveForever('item.fails', 'data'));
    }

    /** @test */
    public function flushingCacheShouldReturnBoolean()
    {
        $driver = $this->newDriver();

        $this->assertTrue($driver->flush());
    }

    public function timeProvider()
    {
        return [
            [60]
        ];
    }

    protected function newDriver()
    {
        return new ApcDriver;
    }

    protected function setUp()
    {
        include_once dirname(__DIR__).'/Fixures/helper.php';
        include_once dirname(__DIR__).'/Fixures/apchelper.php';
    }
}
