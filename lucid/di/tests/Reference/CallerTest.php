<?php

namespace Lucid\DI\Tests\Reference;

use Lucid\DI\Reference\Caller;

class CallerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\DI\Reference\CallerInterface', new Caller($this->mockServiceRef(), 'method'));
    }

    private function mockServiceRef()
    {
        return $this->getMockbuilder('Lucid\DI\Reference\ServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
