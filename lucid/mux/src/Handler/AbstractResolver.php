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
    public function setContainer(ContainerInterface $container) : void
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($handler) : Reflector
    {
        try {
            return new Reflector($this->findHandler($handler));
        } catch (\TypeError $e) {
            throw new ResolverException('No routing handler could be found.');
        }
    }

    /**
     * Finds callable handler
     *
     * @param string|callable $handler
     *
     * @return callable
     */
    abstract protected function findHandler($handler) : callable;

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
     * @return mixed|null
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Interop\Container\Exception\NotFoundException
     */
    protected function getService(string $id)
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
    protected function newResolverException($msg) : ResolverException
    {
        return new ResolverException('Can\'t resolve handler: '. $msg);
    }
}
