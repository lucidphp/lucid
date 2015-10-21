<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Resolver;

use Lucid\DI\ServiceInterface;
use Lucid\Config\ParameterInterface;
use Lucid\DI\ContainerBuilderInterface;

/**
 * @interface ResolverInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResolverInterface
{
    /**
     * resolve
     *
     * @param string $id
     * @param ServiceInterface $service
     *
     * @return Object
     */
    public function resolve($id, ContainerBuilderTest $container, ParameterInterface $params = null);

    /**
     * Check if the service binding is invalid for the current resolve cicle.
     *
     * @param ServiceInterface $service
     *
     * @return boolean returns true if this service has bindings and is directly requested,
     * or the current resolve cicle doesn't allow to access this service. Otherwise, false.
     *
     * @return bool
     */
    public function isBoundService(ServiceInterface $service);
}
