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

use Interop\Container\ContainerInterface;
use Lucid\Mux\Exception\ResolverException;

/**
 * @class AbstractResolver
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractResolver implements ContainerAwareResolverInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($handler)
    {
        if (is_callable($callable = $this->findHandler($handler))) {
            return new Reflector($callable);
        };

        throw new ResolverException('No routing handler could be found.');
    }

    /**
     * Finds callable handler
     *
     * @param string|callable $handler
     *
     * @return callable
     */
    abstract protected function findHandler($handler);

    /**
     * getContainer
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $id
     *
     * @return Object|null
     */
    protected function getService($id)
    {
        $container = $this->getContainer();

        if (null !== $container && $container->has($id)) {
            return $container->get($id);
        }

        return null;
    }

    /**
     * newResolverException
     *
     * @param string $msg
     *
     * @return ResolverException
     */
    protected function newResolverException($msg)
    {
        return new ResolverException('Can\'t resolve handler: '. $msg);
    }
}
