<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Handler;

use Interop\Container\ContainerInterface;
use Lucid\Mux\Exception\ResolverException;

/**
 * @class ControllerResolver
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Resolver extends AbstractResolver
{
    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        null !== $container && $this->setContainer($container);
    }

    /**
     * {@inheritdoc}
     */
    protected function findHandler($handler) : callable
    {
        // if the handler is callable, return it immediately:
        if (is_callable($handler)) {
            return $handler;
        }

        // on this point, not being a string = not callable
        if (!is_string($handler)) {
            throw new ResolverException(sprintf('Cannot resolver handler of type "%s".', gettype($handler)));
        }

        list ($handler, $method) = $this->resolveHandlerAndMethod($handler);

        // if the service parameter is registered as service, return the
        // service object and its method as callable:
        if ($service = $this->getService($handler)) {
            return null === $method ? $service : [$service, $method];
        }

        if (!class_exists($handler)) {
            return [$handler, $method];
        }

        // kay thx bye
        try {
            return [new $handler, $method];
        } catch (\Throwable $t) {
            throw $this->newResolverException($t->getMessage());
        }
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
}
