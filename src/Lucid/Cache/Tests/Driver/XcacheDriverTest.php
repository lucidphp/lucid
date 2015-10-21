<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Tests\Driver;

use Lucid\Cache\Driver\XcacheDriver;

/**
 * @class XcacheDriverTest
 * @see DriverTest
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class XcacheDriverTest extends DriverTest
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

    public function timeProvider()
    {
        return [
            [60]
        ];
    }

    /** @test */
    public function flushingCacheShouldReturnBoolean()
    {
        $driver = $this->newDriver();
        $this->assertTrue($driver->flush());
    }

    protected function newDriver()
    {
        return new XcacheDriver;
    }

    protected function setUp()
    {
        include_once dirname(__DIR__).'/Fixures/helper.php';
        include_once dirname(__DIR__).'/Fixures/xcachehelper.php';
    }
}
