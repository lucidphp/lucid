<?php

/*
 * This File is part of the Lucid\Module\Template\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Tests;

use Lucid\Module\Template\Section;

/**
 * @class SectionTest
 *
 * @package Lucid\Module\Template\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldCaptureOuput()
    {
        $section = new Section;

        $section->start();
        echo 'Foo';
        $section->stop();

        $this->assertSame('Foo', $section->getContent());
    }

    /** @test */
    public function itShouldResetBuffer()
    {
        $section = new Section;

        $section->start();
        echo 'Foo';
        $section->stop();

        $this->assertSame('Foo', $section->getContent());

        $section->reset();

        $this->assertSame('', $section->getContent());
    }

    /**
     * @test
     * @expectedException \OutOfBoundsException
     */
    public function itShouldThrowExceptionIfBufferIndexIsOutOfRange()
    {
        $section = new Section;

        $section->start();
        echo 'Foo';
        $section->stop();

        $section->getContent(1);
    }

    /** @test */
    public function itShouldGetContentAsArray()
    {
        $section = new Section;

        $section->start();
        echo 'Foo';
        $section->stop();
        $section->start();
        echo 'Bar';
        $section->stop();

        $this->assertEquals(['Foo', 'Bar'], $section->getContent(null, true));
    }

    /** @test */
    public function itShouldGetContentBufferAtGivenIndex()
    {
        $section = new Section;

        $section->start();
        echo 'Foo';
        $section->stop();
        $section->start();
        echo 'Bar';
        $section->stop();

        $this->assertSame('Foo', $section->getContent(0));
        $this->assertSame('Bar', $section->getContent(1));
    }
}
