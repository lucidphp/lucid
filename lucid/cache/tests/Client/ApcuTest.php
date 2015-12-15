<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Tests\Client;

use Lucid\Cache\Client\Apcu;

/**
 * @class ApcuTest
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ApcuTest extends AbstractClientTest
{
    /** @test */
    public function itShouldParseMinutesToSeconts()
    {
        $driver = $this->newClient();

        $this->assertSame(60, $driver->parseExpireTime(1));
    }

    /** @test */
    public function itShouldParseDateToSeconds()
    {
        $driver = $this->newClient();

        $this->assertSame(60, $driver->parseExpireTime('60 seconds'));
    }

    /** @test */
    public function storingItemsShouldReturnBoolean()
    {
        $driver = $this->newClient();

        $this->assertTrue($driver->saveForever('item.exists', 'data'));
        $this->assertFalse($driver->saveForever('item.fails', 'data'));
    }

    /** @test */
    public function flushingCacheShouldReturnBoolean()
    {
        $driver = $this->newClient();

        $this->assertTrue($driver->flush());
    }

    public function timeProvider()
    {
        return [
            [60]
        ];
    }

    protected function newClient()
    {
        return new Apcu;
    }

    protected function setUp()
    {
        include_once dirname(__DIR__).'/Fixures/helper.php';
        include_once dirname(__DIR__).'/Fixures/apcuhelper.php';
    }
}
