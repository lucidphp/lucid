<?php

namespace Lucid\Writer\Tests\File;

use Lucid\Writer\File\JsonGenerator;

class JsonGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldGenerateJsonStrings()
    {
        $js = new JsonGenerator;
        $js->addContent('foo', 'bar');
        $this->assertJsonStringEqualsJsonString(json_encode(['foo' => 'bar']), $js->generate());
        $js = new JsonGenerator;
        $js->setContent($data = [
            'foo' => [
            'bar' => [
                ]
            ]
        ]);
        $this->assertJsonStringEqualsJsonString(json_encode($data), $js->generate());
        $js = new JsonGenerator;
        $js->setContent([
            'foo' => [
            'bar' => [
                ]
            ]
        ]);
        $js->addContent('foo.bar', 'baz');
        $this->assertJsonStringEqualsJsonString(json_encode(['foo' => ['bar' => 'baz']]), $js->generate());
    }
}
