<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Handler;

use RuntimeException;
use Interop\Container\ContainerInterface;
use Lucid\Mux\Exception\ResolverException;

/**
 * @class ControllerResolver
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Resolver implements ContainerAwareResolverInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($controller)
    {
        if (is_callable($callable = $this->findHandler($controller))) {
            return new Reflector($callable);
        };

        throw new RuntimeException('No routing handler could be found.');
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * resolveHandlerAndMethod
     *
     * @param string $handler
     *
     * @return array
     */
    protected function resolveHandlerAndMethod($handler)
    {
        list ($handler, $method) = array_pad(explode('@', $handler), 2, null);

        return [$handler, $method];
    }

    /**
     * findHandler
     *
     * @param mixed $handler
     *
     * @return callable
     */
    private function findHandler($handler)
    {
        // if the handler is callable, return it immediately:
        if (is_callable($handler)) {
            return $handler;
        }

        if (!is_string($handler)) {
            throw new ResolverException(sprintf('Cannot resolver handler of type "%s".', gettype($handler)));
        }

        list ($handler, $method) = $this->resolveHandlerAndMethod($handler);

        // if the service parameter is registererd as service, return the
        // service object and its method as callable:
        if ($service = $this->getService($handler)) {
            return [$service, $method];
        }

        if (class_exists($handler)) {
            try {
                return [new $handler, $method];
            } catch (\Throwable $e) {
                throw new ResolverException('Cannot resolve handler', $e->getCode(), $e);
            }
        }

        return [$handler, $method];
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    private function getService($id)
    {
        if (null !== $this->container && $this->container->has($id)) {
            return $this->container->get($id);
        }

        return null;
    }
}
