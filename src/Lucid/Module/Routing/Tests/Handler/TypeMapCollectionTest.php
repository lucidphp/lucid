<?php

/*
 * This File is part of the Lucid\Module\Routing\Tests\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Tests\Handler;

use Mockery as m;
use Lucid\Module\Routing\Handler\TypeMapCollection;

/**
 * @class TypeMapCollectionTest
 *
 * @package Lucid\Module\Routing\Tests\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TypeMapCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldHandleTypeMapper()
    {
        $mapper = $this->mockMapper();
        $mapper->shouldReceive('getType')->andReturn('stdClass');
        $mapper->shouldReceive('getObject')->andReturn($obj = new \stdClass);

        $tc = new TypeMapCollection([$mapper]);

        $this->assertTrue($tc->has('stdClass'));
        $this->assertSame($obj, $tc->get('stdClass'));
        $this->assertSame($mapper, $tc->getMapper('stdClass'));
        $this->assertNull($tc->getMapper('someClass'));
    }

    protected function mockMapper()
    {
        return m::mock('Lucid\Module\Routing\Handler\TypeMapperInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
