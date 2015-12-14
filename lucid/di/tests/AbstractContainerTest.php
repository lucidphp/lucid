<?php

/**
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests;

use Lucid\DI\ContainerInterface;
use Lucid\DI\Exception\ContainerException;

/**
 * @class AbstractContainerTest
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldHaveReadOnlyParameters()
    {
        $container = $this->newContainer();

        try {
            $container->parameters = 'newParams';
        } catch (ContainerException $e) {
            $this->assertEquals('Cannot set READ ONLY properties.', $e->getMessage());

            return;
        }

        $this->fail('properties should be readable only.');
    }

    /** @test */
    public function itShouldBePossibleToSetAServiceObject()
    {
        $service = $this->getMock('MyServiceObject'.$this->mockPosFix());
        $container = $this->newContainer();

        $this->assertFalse($container->has('my_service'));

        $container->set('my_service', $service);
        $this->assertTrue($container->has('my_service'));
    }

    /** @test */
    public function itShouldBeAbleToRetrieveServices()
    {
        $service = $this->getMock('MyServiceObject'.$this->mockPosFix());
        $container = $this->newContainer();
        $container->set('my_service', $service);

        $this->assertTrue($service === $container->get('my_service'));
    }

    /** @test */
    public function itShouldThrowExceptionIfNotSettingsServicesTwice()
    {
        $container = $this->newContainer();
        $container->set('foo', new \stdClass);

        try {
            $container->set('foo', new \stdClass);
        } catch (ContainerException $e) {
            $this->assertSame(
                sprintf('Service "foo" is alread set. Use "%s::replace()", or pass "$forceReplace".', get_class($container)),
                $e->getMessage()
            );

            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldReplaceServices()
    {
        $container = $this->newContainer();
        $container->set('foo', new \stdClass);

        $this->assertNull($container->set('foo', new \stdClass, ContainerInterface::FORCE_REPLACE_ON_DUPLICATE));
        $this->assertNull($container->replace('foo', new \stdClass));
    }

    /** @test */
    public function replaceShouldThrowExceptionIfIdDoesNotExist()
    {
        $container = $this->newContainer();
        try {
            $container->replace('foo', new \stdClass);
        } catch (ContainerException $e) {
            $this->assertSame('Service "foo" cannot be replaced, as it doesn\'t exist.', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldForceReplaceService()
    {
        $container = $this->newContainer();
        $this->assertNull($container->replace('foo', new \stdClass, false));
    }

    /**
     * Get a new container instance.
     *
     * @return Lucid\DI\ContainerInterface
     */
    abstract protected function newContainer();

    /**
     * mockPosFix
     *
     * @return string
     */
    protected function mockPosFix()
    {
        return strtr(microtime(), ['.' => '', ' ' => '']);
    }
}
