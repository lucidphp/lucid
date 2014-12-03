<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Tests\Session\Data;

use Lucid\Module\Http\Session\Data\MetaData;

/**
 * @class MetaDataTest
 *
 * @package Selene\Adapter\Http\Tests\Session\Data
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MetaDataTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $instance = new MetaData;

        $this->assertInstanceof('Lucid\Module\Http\ParameterInterface', $instance);
        $this->assertInstanceof('Lucid\Module\Http\Session\Data\AttributesInterface', $instance);
    }

    /** @test */
    public function itShouldGenerateLifetimeAsUnixTimestamp()
    {
        $time = time();
        $meta = new MetaData('meta_key', 120);

        $this->assertSame($time, $meta->getCreationTimestamp());
        $this->assertSame($time, $meta->getLastUsedTimestamp());
        $this->assertSame($time + (120 * 60), $meta->get('__TTL__'));
    }

    /** @test */
    public function itShouldHaveCertainKeysAfterInitialization()
    {
        $meta = new MetaData;

        $keys = $meta->keys();

        $this->assertContains('__CREATED__', $keys);
        $this->assertContains('__UPDATED__', $keys);
        $this->assertContains('__TTL__', $keys);
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $meta = new MetaData('meta_key', 120);
        $time = time();

        $data = [
            '__CREATED__' => $time - 20
        ];

        $meta->initialize($data);
    }
}
