<?php

/**
 * This File is part of the Lucid\DI\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests;

use Lucid\DI\Container;
use Lucid\DI\Exception\ContainerException;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeReadOnly()
    {
        $container = $this->newContainer();
        try {
            $container->parameters = 'newParams';
        } catch (ContainerException $e) {
            $this->assertEquals('Cannot set READ ONLY properties.', $e->getMessage());

            return;
        }
        $this->fail();
    }

    protected function newContainer()
    {
        return new Container;
    }
}
