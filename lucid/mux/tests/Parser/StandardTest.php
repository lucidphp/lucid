<?php

namespace Lucid\Mux\Tests\Parser;

use Lucid\Mux\Route;
use Lucid\Mux\Parser\Standard as Parser;
use Lucid\Mux\Parser\DefaultParser;
use Lucid\Mux\Parser\ParserInterface;
use Lucid\Mux\Exception\ParserException;

class StandardTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldTokenizePattern()
    {
        $r = $this->mockRoute();
        $r->method('getPattern')->willReturn('/foo/bar/{a}~bar;{b?}');

        $t = Parser::tokenizePattern($r->getPattern(), false);

        $this->assertSame(7, count($t));

        array_map(function ($thing) {
            $this->assertInstanceOf('Lucid\Mux\Parser\TokenInterface', $thing);
        }, $t);
    }

    /** @test */
    public function parseShouldReturnRouteContext()
    {
        $r = $this->mockRoute();
        $r->method('getPattern')->willReturn('/foo/bar/{a?}');
        $this->prepareRoute($r, [], ['a' => '(\d+)']);

        $ctx = Parser::parse($r);
        $this->assertInstanceOf('Lucid\Mux\RouteContextInterface', $ctx);
    }

    /**
     * @test
     * @dataProvider patternMatchProvider
     */
    public function transpiledExpressionsShouldMatch($pattern, $host, array $matches, array $cns = [], array $def = [])
    {
        extract(Parser::transpilePattern($pattern, $host, $cns, $def));

        $delim = ParserInterface::EXP_DELIM;
        $regex = sprintf('%1$s^%2$s$%1$s', $delim, $expression);

        foreach ($matches as $m) {
            list($path, $match)  = array_pad((array)$m, 2, true);

            if ($match !== $matched = (bool)preg_match_all($regex, $path)) {
                $reason = $match ? 'should' : 'shouldn\'t';
                $this->fail(sprintf('Regex %s %s match path %s', $regex, $reason, $path));
            }

            $this->assertSame($match, $matched);
        }
    }

    public function patternMatchProvider()
    {
        return [
            [
                'foo/{bar}', false,
                [['/foo/bar', true], ['/foo/baz', true], ['/foo/bar/str', false]]
            ],
            [
                '/foo/{bar?}', false,
                [['/foo/bar', false], ['/foo/12', true]],
                ['bar' => '\d+']
            ],
            [
                '/foo/{bar?}{baz?}', false,
                [['/foo/bar', false], ['/foo/12', true], ['/foo/12a', true]],
                ['bar' => '\d+', 'baz' => '\w+']
            ],
            [
                '/foo/{bar}/baz', false,
                [['/foo/bar/baz', true], ['/foo/baz', false], ['/foo/12/baz', true]]
            ],
            [
                '/{file}.jpg', false,
                [['/image.jpg', true], ['/image.gif', false]]
            ],
            [
                '/{file}.{ext}', false,
                [['/image.jpeg', true], ['/image.jpg', true], ['/image.png', true], ['/image.gif', false]],
                ['ext' => 'jpe?g|png']
            ],
        ];
    }

    private function mockRoute()
    {
        return $this->getMockbuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();
    }


    /**
     * prepareRoute
     *
     * @param Mock  $route
     * @param array $def
     * @param array $const
     *
     * @return void
     */
    private function prepareRoute($route, array $def = null, array $const = null)
    {
        if (null !== $def) {
            $route->method('getDefaults')->willReturn($def);
            $dmap = [];
            foreach ($def as $key => $value) {
                $dmap[] = [$key, $value];
            }

            $route->method('getDefault')->will($this->returnValueMap($dmap));
        }

        if (null !== $const) {
            $route->method('getConstraints')->willReturn($const);
            $cmap = [];
            foreach ($const as $key => $value) {
                $cmap[] = [$key, $value];
            }

            $route->method('getConstraint')->will($this->returnValueMap($cmap));
        }
    }
}
