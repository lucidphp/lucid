<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI;

use Lucid\DI\Definition\ServiceInterface;

/**
 * @interface ContainerBuilderInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ContainerBuilderInterface
{
    /**
     * Define a service
     *
     * @param string $id
     * @param string $class
     * @param array $arguments
     * @param int   $scope
     *
     * @return ServiceInterface
     */
    public function define($id, $class, array $arguments = [], $scope = Scope::SINGLETON);

    /**
     * Register a service object by id.
     *
     * @param string $id
     * @param ServiceInterface $service
     *
     * @return void
     */
    public function register($id, ServiceInterface $service);

    /**
     * Get a service definition by id.
     *
     * @param string $id
     *
     * @return ServiceInterface
     */
    public function getService($id);

    /**
     * hasService
     *
     * @param string $id
     *
     * @return bool
     */
    public function hasService($id);

    /**
     * Get all services.
     *
     * @return array `Lucid\DI\Definition\ServiceInterface[]`
     */
    public function getServices();

    /**
     * Build the container.
     *
     * @param ContainerReflectorInterface $reflector
     * @param ContainerTargetInterface $target
     *
     * @return ContainerInterface
     */
    public function build(ContainerReflectorInterface $reflector = null, ContainerTargetInterface $target = null);
}
