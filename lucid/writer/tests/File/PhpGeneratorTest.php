<?php

namespace Lucid\Writer\Tests\File;

use Lucid\Writer\File\PhpGenerator;

class PhpGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldDoThings()
    {
        $gen = new PhpGenerator;
        $gen->addString('return ');
        $gen->addArray(['foo' => 'bar']);
        $gen->addString('; ');

        $ret = $gen->generate(true);
    }
}
