<?php

namespace Lucid\Writer\Tests\Object;

use Lucid\Writer\Object\CommentBlock;

class CommentBlockTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('Lucid\Writer\Object\DocBlock', new CommentBlock);
    }

    /** @test */
    public function itShouldWriteCommentBlock()
    {
        $comment = new CommentBlock;
        $comment->addAnnotation('foo', 'bar');

        $this->assertSame("/*\n * @foo bar\n */", (string)$comment->generate());
    }

    /** @test */
    public function itShouldInlineComments()
    {
        $comment = new CommentBlock;
        $comment->addAnnotation('foo', 'bar');
        $comment->setInline(true);

        $this->assertSame("/* @foo bar */", (string)$comment->generate());
    }
}
