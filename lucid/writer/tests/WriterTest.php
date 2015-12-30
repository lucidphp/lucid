<?php

/**
 * This File is part of the Lucid\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Tests;

use Lucid\Writer\Writer;

/**
 * @class WriterTest
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldUseSpacesForIndentation()
    {
        $wr = new Writer;
        $wr
            ->writeln('foo')
            ->indent()
            ->writeln('bar');

        $this->assertSame("foo\n    bar", $wr->dump());
    }

    /** @test */
    public function itShouldBeStringable()
    {

        $this->assertSame('', (string)(new Writer));
    }

    /** @test */
    public function itShouldBeAbleToUseTabs()
    {
        $wr = new Writer;
        $tab = chr(11);
        $wr->useTabs();

        $wr
            ->writeln('foo')
            ->indent()
            ->writeln('bar');

        $this->assertSame("foo\n".$tab."bar", $wr->dump());
    }

    /** @test */
    public function itShouldBeAbleToRemoveLines()
    {
        $wr = new Writer;
        $wr
            ->writeln('foo')
            ->writeln('bar')
            ->writeln('baz');

        $wr->removeln(1);

        $this->assertSame("foo\nbaz", $wr->dump());
    }

    /** @test */
    public function itShouldPopLines()
    {
        $wr = new Writer;
        $wr
            ->writeln('foo')
            ->writeln('bar')
            ->writeln('baz');

        $wr->popln();

        $this->assertSame("foo\nbar", $wr->dump());
    }

    /** @test */
    public function itShouldAbleToReplaceLines()
    {
        $wr = new Writer;
        $wr
            ->writeln('foo')
            ->writeln('bar')
            ->writeln('baz');

        $wr->replaceln('bam', 1);

        $this->assertSame("foo\nbam\nbaz", $wr->dump());
    }

    /** @test */
    public function itShouldThrowErrorWhenIndexIsWronOnReplace()
    {
        $wr = new Writer;
        $wr
            ->writeln('foo');

        try {
            $wr->replaceln('bar', 1);
        } catch (\OutOfBoundsException $e) {
            $this->assertSame('Lucid\Writer\Writer::replaceln: undefined index "1".', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldThrowErrorWhenIndexIsWronOnRemove()
    {
        $wr = new Writer;
        $wr
            ->writeln('foo');

        try {
            $wr->removeln(1);
        } catch (\OutOfBoundsException $e) {
            $this->assertSame('Lucid\Writer\Writer::removeln: undefined index "1".', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldAllowTrailingSpaces()
    {
        $wr = new Writer;
        $wr
            ->writeln('foo ');

        $this->assertSame('foo', $wr->dump());

        $wr = new Writer;
        $wr->allowTrailingSpace(true);
        $wr
            ->writeln('foo ');

        $this->assertSame('foo ', $wr->dump());
    }

    /** @test */
    public function defaultOutputIndentShouldBeZero()
    {
        $wr = new Writer;

        $this->assertSame(0, $wr->getOutputIndentation());
    }

    /** @test */
    public function itShouldAddNoExtraSpace()
    {
        $wr = new Writer;

        $wr->writeln('foo')
            ->writeln('bar')
            ->indent()
            ->replaceln('', 1)
        ->writeln('baz');

        $this->assertSame("foo\n\n    baz", $wr->dump());
    }

    /** @test */
    public function itShouldThrowExceptionOnNoneStrangableValues()
    {
        $wr = new Writer;

        try {
            $wr->writeln([]);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Input value must be stringable.', $e->getMessage());
            return;
        }

        $this->fail();
    }
}
