<?php

/*
 * This File is part of the Lucid\Module\Template\Tests\Data package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Tests\Data;

use Lucid\Module\Template\Data\Data;

/**
 * @class DataTest
 *
 * @package Lucid\Module\Template\Tests\Data
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Module\Template\Data\TemplateDataInterface', new Data);
    }

    /** @test */
    public function dataShouldBeSettable()
    {
        $data = new Data;
        $data->set(['foo' => 'bar']);

        $this->assertSame('bar', $data->get('foo'));
    }

    /** @test */
    public function itShouldMergeData()
    {
        $data = new Data;
        $data->set(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $data->all(['bar' => 'baz']));
    }

    /** @test */
    public function itShouldNotMergeDataIfReplaced()
    {
        $data = new Data;
        $data->replace(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $data->all(['bar' => 'baz']));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function keysMustNotBeNumeric()
    {
        $data = new Data;
        $data->add(0, 'bar');
    }
}
