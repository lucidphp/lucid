<?php

/*
 * This File is part of the Lucid\Mux\Tests\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Tests\Handler;

use Lucid\Mux\Handler\TypeMapCollection;

/**
 * @class TypeMapCollectionTest
 *
 * @package Lucid\Mux\Tests\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TypeMapCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldHandleTypeMapper()
    {
        $mapper = $this->mockMapper();
        $mapper->expects($this->any())->method('getType')->willReturn('stdClass');
        $mapper->expects($this->any())->method('getObject')->willReturn($obj = new \stdClass);

        $tc = new TypeMapCollection([$mapper]);

        $this->assertTrue($tc->has('stdClass'));
        $this->assertSame($obj, $tc->get('stdClass'));
        $this->assertTrue($mapper === $tc->getMapper('stdClass'));
        $this->assertNull($tc->getMapper('someClass'));
    }

    protected function mockMapper()
    {
        return $this->getMockBuilder('Lucid\Mux\Handler\TypeMapperInterface')
            ->disableOriginalConstructor()->getMock();
    }
}
