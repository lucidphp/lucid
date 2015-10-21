<?php

/**
 * This File is part of the Lucid\DI\Tests\Resolver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Tests\Resolver;

use Lucid\DI\Service;
use Lucid\DI\Reference\CallerReference;
use Lucid\DI\Reference\ServiceReference;
use Lucid\DI\Resolver\ReflectionResolver;

class ReflectionResolverTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldResolveNestedArguments()
    {
        $resolver = new ReflectionResolver;
        $serviceA = new Service($class = 'Lucid\DI\Tests\Stubs\SimpleService');
        $serviceB = new Service($class = 'Lucid\DI\Tests\Stubs\SimpleService');


        $serviceA->setArguments([['service' => new ServiceReference('my_service_b')]]);

        var_dump($resolver->resolve('service_a', $serviceA));
    }

    /** @test */
    public function itShouldResolveCalersInArguments()
    {
        //$container = $this->newContainer();
        //$container->register('a', $s = new Service($class = __NAMESPACE__.'\Stubs\SimpleService'));
        //$container->register('b', new Service($class = __NAMESPACE__.'\Stubs\CalledService'));

        //$s->setArguments([new CallerReference(new ServiceReference('b'), 'call', ['ok'])]);

        //$serviceA = $container->get('a');
        //$serviceB = $container->get('b');

        //$this->assertSame('ok', $serviceB->value);
    }
}
