<?php

namespace Lucid\Common\Tests\Helper;

use Lucid\Common\Helper\Str;

class StrTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function callingLowdashShouldTriggerDeprecationError()
    {
        try {
            Str::lowDash('fooBar');
        } catch (\Throwable $e) {
            $this->assertInstanceOf('PHPUnit_Framework_Error_Deprecated', $e);
        } catch (\Exception $e) {
            $this->fail();
        }
    }
    
    /** @test */
    public function itShouldSafeCompareStrings()
    {
        $a = 'secret';
        $b = 'secret';
        $this->assertTrue(Str::safeCmp($a, $b));
        $a = 'secret';
        $b = 'somesecretkey';
        $this->assertFalse(Str::safeCmp($a, $b));
        $a = 'secret';
        $b = 'secrut';
        $this->assertFalse(Str::safeCmp($a, $b));
    }

    /** @test */
    public function itShouldTellIfStringEqulasString()
    {

        $a = 'string';
        $b = 'string';

        $this->assertTrue(Str::equals($a, $b));

        $a = 'string';
        $b = 'otherstring';

        $this->assertFalse(Str::equals($a, $b));
    }

    /** @test */
    public function itShouldCamelCaseStrings()
    {
        $this->assertEquals('fooBar', Str::camelCase('foo_bar'));
    }

    /** @test */
    public function strcamelCaseAll()
    {
        $this->assertEquals('FooBar', Str::camelCaseAll('foo_bar'));
    }

    /** @test */
    public function itShouldSnakeCaseStrings()
    {
        $this->assertEquals('foo_bar', Str::snakeCase('fooBar'));
        $this->assertEquals('foo_bar_baz', Str::snakeCase('fooBarBaz'));
    }

    /** @test */
    public function itShouldSnakeCaseStringsUsingACustomDelimiter()
    {
        $this->assertEquals('foo@bar', Str::snakeCase('fooBar', '@'));
        $this->assertEquals('foo:bar:baz', Str::snakeCase('fooBarBaz', ':'));
    }

    /**
     * @test
     * @dataProvider strrandLengthProvider
     */
    public function strrand($length)
    {
        $this->assertSame($length, strlen(Str::rand($length)));
    }

    /**
     * @test
     * @dataProvider strrandLengthProvider
     */
    public function strquickRand($length)
    {
        $this->assertSame($length, strlen(Str::quickRand($length)));
    }

    public function strrandLengthProvider()
    {
        return [
            [12],
            [22],
            [40],
            [25],
            [125]
        ];
    }
}
