<?php

/**
 * This File is part of the Lucid\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Tests\Object;

use Lucid\Writer\Object\DocBlock;

/**
 * @class DocBlockTest
 * @package Lucid\Writer
 * @version $Id$
 */
class DocBlockTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldGenerateBlockComments()
    {
        $this->assertSame("/**\n */", (new DocBlock)->generate());
    }

    /** @test */
    public function itShouldAddDescription()
    {
        $doc = new DocBlock;
        $doc->setDescription('test');

        $this->assertSame("/**\n * test\n */", $doc->generate());
    }

    /** @test */
    public function itShouldAddLongDescription()
    {
        $expected = <<<PHP
/**
 * test
 *
 * A
 * B
 */
PHP;
        $doc = new DocBlock;
        $doc->setDescription('test');
        $doc->setLongDescription("A\nB");

        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldAddAnnotations()
    {
        $expected = <<<PHP
/**
 * @name foo
 * @param string \$bar
 */
PHP;
        $doc = new DocBlock;
        $doc->addAnnotation('name', 'foo');
        $doc->addParam('string', 'bar');

        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldWriteFullBlock()
    {

        $expected = <<<PHP
/**
 * Foo
 *
 * Bar
 * Baz
 *
 * @name foo
 * @param string \$bar
 *
 * @return string|null description
 */
PHP;
        $doc = new DocBlock;
        $doc->setDescription('Foo');
        $doc->setLongDescription("Bar\nBaz");
        $doc->addAnnotation('name', 'foo');
        $doc->addParam('string', 'bar');
        $doc->setReturn('string|null', 'description');

        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldWriteDescAndReturn()
    {

        $expected = <<<PHP
/**
 * Foo
 *
 * @return void
 */
PHP;
        $doc = new DocBlock;
        $doc->setDescription('Foo');
        $doc->setReturn('void');
        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldWriteNewLinesOnAnnotations()
    {

        $expected = <<<PHP
/**
 * @name foo
 *
 * @name bar
 */
PHP;
        $doc = new DocBlock;
        $doc->setAnnotations([
            ['name', 'foo'],
            null,
            ['name', 'bar']
        ]);
        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldInlineBlocks()
    {
        $expected = '/** @test */';

        $doc = new DocBlock;
        $doc->addAnnotation('test');
        $doc->setInline(true);

        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldOverrideInlineIfDiscriptionOrAnnotations()
    {

        $expected = <<<PHP
/**
 * @foo foo
 * @bar bar
 */
PHP;
        $doc = new DocBlock;
        $doc->setAnnotations([
            ['foo', 'foo'],
            ['bar', 'bar']
        ]);
        $doc->setInline(true);
        $this->assertSame($expected, $doc->generate());

        $expected = <<<PHP
/**
 * FooBar
 *
 * @bar bar
 */
PHP;
        $doc = new DocBlock;
        $doc->setDescription('FooBar');
        $doc->setAnnotations([
            ['bar', 'bar']
        ]);
        $doc->setInline(true);
        $this->assertSame($expected, $doc->generate());
    }
}
