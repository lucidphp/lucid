<?php

namespace Lucid\Mux\Tests\Parser;

use Lucid\Mux\Route;
use Lucid\Mux\Parser\Standard as Parser;

class StandardTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldTranspileRouteExpression()
    {
        $r1 = 'foo/bar/{user}/{id?}/{area}/restofstr';

        $route = $this->mockRoute();
        $route->method('getPattern')->willReturn($r1);
        $route->method('getConstraints')->willReturn(['id' => '(\d+)']);
        $route->method('getDefault')->will($this->returnValueMap([['id', '(\d+)']]));

        $context = Parser::parse($route);
    }

    private function mockRoute()
    {
        return $this->getMockbuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
