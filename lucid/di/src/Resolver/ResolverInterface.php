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
     * Resolves an service object by a given id.
     *
     * @param string $id
     * @param ServiceInterface $service
     * @throws Lucid\DI\Exception\ResolverException if theres an error
     * while resolving the service.
     *
     * @return Object returns an `object`.
     */
    public function resolve($id, ContainerBuilderInterface $container, ParameterInterface $params = null);
}
