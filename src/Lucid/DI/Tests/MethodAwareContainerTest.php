<?php

/*
 * This File is part of the Lucid\DI\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests;

use Lucid\DI\Tests\Stubs\MethodContainer;

/**
 * @class FactoryContainerTest
 *
 * @package Lucid\DI\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MethodAwareContainerTest extends ContainerTest
{
    /** @test */
    public function itShouldFindAServiceByMethod()
    {
        $container = $this->newContainer();

        $this->assertTrue($container->has('my_service'));
        $this->assertInstanceof('stdClass', $container->get('my_service'));
    }

    protected function newContainer()
    {
        return new MethodContainer;
    }
}
