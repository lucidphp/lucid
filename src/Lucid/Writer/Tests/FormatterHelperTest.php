<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Writer\Tests;

use Lucid\Writer\FormatterHelper;

/**
 * @class FormatterHelperTest
 * @see PHPUnit_Framework_TestCase
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FormatterTraitTest extends \PHPUnit_Framework_TestCase
{
    use FormatterHelper;

    /** @test */
    public function itShouldFormatIntents()
    {
        $this->assertSame('    ', $this->indent(4));
        $this->assertSame('      ', $this->indent(6));
        $this->assertSame('', $this->indent(0));
    }

    /** @test */
    public function itShouldFormatVariables()
    {

        $var = ['foo' => 'bar'];

        $str = $this->extractParams($var, 0);

        $this->assertEquals("[\n    'foo' => 'bar',\n]", $str);

        $str = $this->extractParams($var, 4);

        $this->assertEquals("    [\n        'foo' => 'bar',\n    ]", $str);

        $var = ['foo' => null];

        $str = $this->extractParams($var, 0);

        $this->assertEquals("[\n    'foo' => null,\n]", $str);

        $var = ['foo' => true];

        $str = $this->extractParams($var, 0);

        $this->assertEquals("[\n    'foo' => true,\n]", $str);

        $var = ['foo' => ['bar' => false]];

        $str = $this->extractParams($var, 0);

        $this->assertEquals("[\n    'foo' => [\n        'bar' => false,\n    ],\n]", $str);

        $var = ['foo' => '$this->doStuff()'];

        $str = $this->extractParams($var, 0);

        $this->assertEquals("[\n    'foo' => \$this->doStuff(),\n]", $str);

        $var = ['foo' => 'bar', 'baz' => ['foof' => 'swosh']];
    }
}
