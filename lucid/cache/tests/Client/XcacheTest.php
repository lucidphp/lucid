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

use ReflectionClass;
use Lucid\Cache\Client\Xcache;

/**
 * @class XcacheClientTest
 * @see ClientTest
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class XcacheTest extends AbstractClientTest
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

    /**
     * {@inheritdoc}
     */
    public function timeProvider()
    {
        return [
            [60]
        ];
    }

    /** @test */
    public function flushingCacheShouldReturnBoolean()
    {
        $driver = $this->newClient();
        $this->assertTrue($driver->flush());
    }

    protected function newClient()
    {
        return (new ReflectionClass('Lucid\Cache\Client\Xcache'))->newInstanceWithoutConstructor();
    }

    protected function setUp()
    {
        include_once dirname(__DIR__).'/Fixures/helper.php';
        include_once dirname(__DIR__).'/Fixures/xcachehelper.php';
    }
}
