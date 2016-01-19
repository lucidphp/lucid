<?php

namespace Lucid\Mux\Tests\Parser;

use Lucid\Mux\Parser\Variable;

class VariableTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeStringable()
    {
        $var = new Variable('foo');
        $this->assertSame('(?P<foo>[^/]++)', (string)$var);

        $var = new Variable('foo', true, '(\d+)');
        $this->assertSame('(?P<foo>(\d+)', (string)$var);
    }
}
