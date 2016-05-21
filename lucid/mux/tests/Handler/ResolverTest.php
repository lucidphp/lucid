<?php

/*
 * This File is part of the Lucid\Mux\Tests\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Tests\Handler;

use Lucid\Mux\Handler\Resolver;
use Lucid\Mux\Exception\ResolverException;
use Lucid\Mux\Tests\Handler\Stubs\SimpleHandler;

/**
 * @class ResolverTest
 *
 * @package Lucid\Mux\Tests\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldParseStaticHandlers()
    {
        $handlerStr = __CLASS__.'::fakeStaticHandle';
        $foulStr    = __CLASS__.'::foulStaticHandle';

        $resolver = new Resolver();
        $this->assertInstanceof('Lucid\Mux\Handler\Reflector', $resolver->resolve($handlerStr));

        try {
            $resolver->resolve($foulStr);
        } catch (\RuntimeException $e) {
            $this->assertSame('No routing handler could be found.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldParseCallables()
    {
        $resolver = new Resolver();
        $this->assertInstanceof(
            'Lucid\Mux\Handler\Reflector',
            $resolver->resolve(
                function () {
                    return true;
                }
            )
        );
    }

    /** @test */
    public function itShouldParseNoneStaticHandlerAnnotation()
    {
        $handlerStr = __NAMESPACE__.'\Stubs\SimpleHandler@noneParamAction';

        $resolver = new Resolver();

        $handler = $resolver->resolve($handlerStr);

        $this->assertInstanceof('Lucid\Mux\Handler\Reflector', $handler);
    }

    /** @test */
    public function itShouldParseHandlerAsService()
    {
        $container = $this->mockContainer(['handler' => $this, 'simple_handler' => new SimpleHandler]);
        $resolver = new Resolver;
        $resolver->setContainer($container);

        $this->assertInstanceof(
            'Lucid\Mux\Handler\Reflector',
            $resolver->resolve('handler@fakeAction')
        );
        $this->assertInstanceof(
            'Lucid\Mux\Handler\Reflector',
            $resolver->resolve('simple_handler@noneParamAction')
        );
    }

    /** @test */
    public function itShouldResolveInvokables()
    {
        $handler = $this->getMockBuilder(__NAMESPACE__.'\InvokableHandler')
            ->setMethods(['__invoke'])->getMock();

        $handler->method('__invoke')->willReturnCallback(function () {
            return 'ok';
        });
        $container = $this->mockContainer(['handler' => $this, 'inv.handler' => $handler]);

        $resolver = new Resolver($container);
        $this->assertInstanceOf('Lucid\Mux\Handler\Reflector', $resolver->resolve('inv.handler'));
    }

    /** @test */
    public function itShouldThrowResolverExceptionIfHandlerIsNotInstantiable()
    {
        $resolver = new Resolver();
        $handler = 'Lucid\Mux\Tests\Handler\Stubs\AbstractHandler@handle';

        try {
            $resolver->resolve($handler);
        } catch (ResolverException $e) {
            $this->assertSame(0, strpos($e->getMessage(), 'Can\'t resolve handler: '));
        }
    }

    private function mockContainer(array $services = [])
    {
        $container = $this->getMockbuilder('Interop\Container\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $gmap = [];

        foreach ($services as $id => $service) {
            $gmap[] = [$id, $service];
        }

        $hmap[] = [$this->any(), false];

        $container->method('get')->will($this->returnValueMap($gmap));
        $container->method('has')->willReturnCallback(function ($key) use ($services) {
            return isset($services[$key]);
        });

        return $container;
    }

    public static function fakeStaticHandle()
    {
        return true;
    }

    public function fakeAction()
    {
        return true;
    }
}
