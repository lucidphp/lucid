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
class Resolver extends AbstractResolver
{
    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function findHandler($handler)
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
            return null === $method ? $service : [$service, $method];
        }

        if (!class_exists($handler)) {
            return [$handler, $method];
        }
        $err = null;

        set_error_handler(function ($errno, $msg) {
            throw $this->newResolverException($msg);
        });

        try {
            $resolvedHandler = [new $handler, $method];
        } catch (ResolverException $e) {
            $err = $e;
        } catch (\Throwable $t) {
            $err = $this->newResolverException($t->getMessage());
        }

        restore_error_handler();

        if (null !== $err) {
            throw $err;
        }

        return $resolvedHandler;
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
