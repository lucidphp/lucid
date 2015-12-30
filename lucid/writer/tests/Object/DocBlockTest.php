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
        $expected = <<<EOL
/**
 * test
 *
 * A
 * B
 */
EOL;
        $doc = new DocBlock;
        $doc->setDescription('test');
        $doc->setLongDescription("A\nB");

        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldAddAnnotations()
    {
        $expected = <<<EOL
/**
 * @name foo
 * @param string \$bar
 */
EOL;
        $doc = new DocBlock;
        $doc->addAnnotation('name', 'foo');
        $doc->addParam('string', 'bar');

        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldWriteFullBlock()
    {

        $expected = <<<EOL
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
EOL;
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

        $expected = <<<EOL
/**
 * Foo
 *
 * @return void
 */
EOL;
        $doc = new DocBlock;
        $doc->setDescription('Foo');
        $doc->setReturn('void');
        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldWriteNewLinesOnAnnotations()
    {

        $expected = <<<EOL
/**
 * @name foo
 *
 * @name bar
 */
EOL;
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

        $expected = <<<EOL
/**
 * @foo foo
 * @bar bar
 */
EOL;
        $doc = new DocBlock;
        $doc->setAnnotations([
            ['foo', 'foo'],
            ['bar', 'bar']
        ]);
        $doc->setInline(true);
        $this->assertSame($expected, $doc->generate());

        $expected = <<<EOL
/**
 * FooBar
 *
 * @bar bar
 */
EOL;
        $doc = new DocBlock;
        $doc->setDescription('FooBar');
        $doc->setAnnotations([
            ['bar', 'bar']
        ]);
        $doc->setInline(true);
        $this->assertSame($expected, $doc->generate());
    }
}
