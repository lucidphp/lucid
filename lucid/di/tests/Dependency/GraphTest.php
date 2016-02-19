<?php

namespace Lucid\DI\Tests\Dependency;

use Lucid\DI\Dependency\Graph;
use Lucid\DI\ContainerBuilder;
use Lucid\DI\Reference\Service as Reference;

class GraphTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsExpectedThat()
    {
        $container = new ContainerBuilder;
        $graph = new Graph;

        $a = $container->define('foo', 'Foo');
        $b = $container->define('bar', 'Bar');
        $a->addArgument(new Reference('bar'));

        $graph->generate($container->getServices());
    }
}
