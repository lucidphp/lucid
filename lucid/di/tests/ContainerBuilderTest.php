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

use Lucid\DI\Scope;
use Lucid\DI\Definition\Service;
use Lucid\DI\Exception\ContainerException;
use Lucid\DI\Reference\Caller as CallerReference;
use Lucid\DI\Reference\Service as ServiceReference;
use Lucid\DI\ContainerBuilder;
use Lucid\DI\Tests\Stubs\StaticFactory;

/**
 * @class ContainerBuilderTest
 *
 * @package Lucid\DI\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerBuilderTest extends AbstractContainerTest
{
    /** @test */
    public function itShouldResolveASimpleServices()
    {
        $container = $this->newContainer();
        $container->register('my_service', $def = $this->mockService());

        $def->method('getClass')->willReturn('stdClass');
        $def->method('isBound')->willReturn(false);
        $def->method('getScope')->willReturn($sc = new Scope);

        $instance = $container->get('my_service');

        $this->assertInstanceof('stdClass', $instance);
        $this->assertSame($instance, $container->get('my_service'));
    }

    /** @test */
    public function itshouldThrowIfServiceIsUndefinedOrBound()
    {
        $container = $this->newContainer();
        $a = $container->define('baz');
        $b = $container->define('zap');
        $b->addBinding(new ServiceReference('baz'));

        try {
            $container->get('foo');
        } catch (\Lucid\DI\Exception\NotFoundException $e) {
            $this->assertEquals('The service "foo" is undefined.', $e->getMessage());
        }

        try {
            $container->get('zap');
        } catch (\Lucid\DI\Exception\ContainerException $e) {
            $this->assertEquals(
                'The service "zap" is undefined or bound to another service and cannot be resolved on it\'s own.',
                $e->getMessage()
            );

            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldCreatePrototypes()
    {
        $container = $this->newContainer();
        $container->register('my_service', $s = new Service($class = __NAMESPACE__.'\Stubs\SimpleService'));

        $s->setScope($sc = new Scope(Scope::PROTOTYPE));

        $instance = $container->get('my_service');

        $this->assertInstanceof($class, $instance);
        $this->assertFalse($instance === $container->get('my_service'));
    }

    /** @test */
    public function resolverShouldBeCalledWithService()
    {
        $resolver = $this
            ->getMockBuilder('Lucid\DI\Resolver\ResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $container = $this->newContainer($resolver);

        $resolver->method('resolve')->willReturnCallback(function (...$args) use ($container) {
            $this->assertSame('my_service', $args[0]);
            $this->assertSame($container, $args[1]);
            $this->assertInstanceOf('Lucid\Config\ParameterInterface', $args[2]);
        });

        $container->register('my_service', $s = new Service($class = __NAMESPACE__.'\Stubs\SimpleService'));
        $container->get('my_service');
    }

    /** @test */
    public function itShouldReturnAllServices()
    {
        $container = $this->newContainer();
        $container->register('a', $a = new Service);
        $container->register('b', $b = new Service);

        $this->assertSame(['a' => $a, 'b' => $b], $container->getServices());
    }

    /** @test */
    public function itShouldGetAServiceFromAFactory()
    {
        $factd = $this->prepareFactory([__NAMESPACE__.'\Stubs\StaticFactory', 'makeA']);

        $container = $this->newContainer();
        //$container->register('my_service', $factd);

        //$instance = $container->get('my_service');

        //$this->assertInstanceof('stdClass', $instance);
        //$this->assertSame($instance, $container->get('my_service'));
    }

    /** @test */
    public function itShouldReturnPrototypedInstancesFromFactories()
    {
        $factd = $this->prepareFactory(
            [__NAMESPACE__.'\Stubs\StaticFactory', 'makeA', true, new Scope(Scope::PROTOTYPE)]
        );

        $container = $this->newContainer();
        $container->register('my_service', $factd);

        $instanceA = $container->get('my_service');
        $instanceB = $container->get('my_service');

        $this->assertTrue($instanceA !== $instanceB);
    }

    /** @test */
    public function itShouldErrorOnBoundServices()
    {
        $container = $this->newContainer();
        $container->register('my_service', $a = new Service($class = __NAMESPACE__.'\Stubs\SimpleService'));
        $container->register('my_other_service', $b = new Service($class = __NAMESPACE__.'\Stubs\SimpleService'));

        $b->addArgument(new ServiceReference('my_service'));
        $a->addBinding(new ServiceReference('my_other_service'));

        $thrown = false;
        try {
            $container->get('my_service');
        } catch (ContainerException $e) {
            $thrown = true;
        }

        $this->assertTrue($thrown);

        //$instance = $container->get('my_other_service');

        //$this->assertInstanceOf($class, $instance->arguments[0]);
        //$this->assertFalse($instance === $instance->arguments[0]);

        // bound service still should not be available.
        $thrown = false;
        try {
            $container->get('my_service');
        } catch (ContainerException $e) {
            $thrown = true;
        }

        $this->assertTrue($thrown);
    }

    /** @test */
    public function itShouldCallSettersOnService()
    {
        $container = $this->newContainer();
        $container->register('my_service', $a = new Service($class = __NAMESPACE__.'\Stubs\CallerService'));

        $a->sets('set', ['ok']);

        $instance = $container->get('my_service');

        $this->assertSame('ok', $instance->value);
    }

    /** @test */
    public function itShouldCallCallersOnService()
    {
        $container = $this->newContainer();
        $container->register('my_service_a', $a = new Service($class = __NAMESPACE__.'\Stubs\CallerService'));
        $container->register('my_service_b', $b = new Service($class = __NAMESPACE__.'\Stubs\CalledService'));

        $a->calls(new CallerReference(new ServiceReference('my_service_b'), 'call', ['ok']));

        $serviceB = $container->get('my_service_b');
        $serviceA = $container->get('my_service_a');

        $this->assertSame('ok', $serviceB->value);
    }

    /** @test */
    public function itShouldPassArgumentsToFactoryMethod()
    {
        $factd = $this->prepareFactory([
            $class = __NAMESPACE__.'\Stubs\StaticFactory',
            'makeB'
        ], ['Foo', 'Bar']);

        $class::$testCase = $this;

        $container = $this->newContainer();
        $container->register('my_service', $factd);

        $instance = $container->get('my_service');

        $this->assertSame('Foo', $instance->getA());
        $this->assertSame('Bar', $instance->getB());
    }

    protected function prepareFactory(array $cargs = [], array $args = [], $setters = [], $callers = [])
    {
        $factd = $this->mockFactory($cargs);

        $factd->method('getArguments')->willReturn($args);
        $factd->method('getSetters')->willReturn($setters);
        $factd->method('getCallers')->willReturn($callers);

        return $factd;
    }

    protected function mockFactory(array $args = [])
    {
        $args = array_replace([null, null, true, new Scope(Scope::SINGLETON)], $args);

        $mock = $this
            ->getMockBuilder('Lucid\DI\Definition\Factory')
            ->setConstructorArgs($args)
            ->getMock();

        $mock->method('getClass')->willReturn($args[0]);
        $mock->method('getFactoryMethod')->willReturn($args[1]);
        $mock->method('isStatic')->willReturn($args[2]);
        $mock->method('getScope')->willReturn($args[3]);

        return $mock;
    }

    protected function mockService(array $args = [], array $setters = [], array $callers = [])
    {
        /** @var mixed */
        $def = $this->getMockbuilder('Lucid\DI\Definition\ServiceInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $def->method('getArguments')->willReturn($args);
        $def->method('getSetters')->willReturn($setters);
        $def->method('getCallers')->willReturn($callers);

        return $def;
    }

    protected function tearDown()
    {
        $class = __NAMESPACE__.'\Stubs\StaticFactory';
        $class::$testCase = null;
    }

    protected function newContainer($resolver = null)
    {
        return new ContainerBuilder($resolver ?: null);
    }
}
