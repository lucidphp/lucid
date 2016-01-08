<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Definition;

use Lucid\DI\Scope;
use Lucid\DI\Reference\CallerInterface as CallerReferenceInterface;
use Lucid\DI\Reference\ServiceInterface as ServiceReferenceInterface;

/**
 * @interface ServiceInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ServiceInterface
{
    /**
     * Set the class of the service
     *
     * @param string $class
     */
    public function setClass($class);

    /**
     * getClass
     *
     * @return string
     */
    public function getClass();

    /**
     * Set the constructor Arguments.
     *
     * @param array $arguments
     *
     * @return void
     */
    public function setArguments(array $arguments);

    /**
     * Get the constructor arguments.
     *
     * @return void
     */
    public function getArguments();

    /**
     * Gets a constructor argument from a given index.
     *
     * @return mixed
     */
    public function getArgument($index = 0);

    /**
     * Sets an argument at a given index.
     *
     * @param mixed $argument
     * @param int $index
     *
     * @return void
     */
    public function setArgument($argument, $index = 0);

    /**
     * Adds an constructor argument to the arguments list.
     *
     * @param mixed $argument
     *
     * @return void
     */
    public function addArgument($argument);

    /**
     * setScope
     *
     * @param int $scope
     *
     * @return void
     */
    public function setScope(Scope $scope);

    /**
     * getScope
     *
     * @return void
     */
    public function getScope();

    /**
     * hasScope
     *
     * @param mixed $scope
     *
     * @return bool
     */
    public function hasScope($scope);

    /**
     * The service definition is an abstract blueprint and cannot be
     * initialized.
     *
     * @return bool
     */
    public function isBlueprint();

    /**
     * hasBluePrint
     *
     * @return bool
     */
    public function hasBluePrint();

    /**
     * The service reference used for blueprinting this service.
     *
     * @param ServiceReferenceInterface $bp
     *
     * @return void
     */
    public function setBlueprint(ServiceReferenceInterface $bp);

    /**
     * Marks the service definition is blueprint and therefor not instantiable.
     *
     * @param boolen $bp
     *
     * @return void
     */
    public function markAsBlueprint($bp);

    /**
     * Merge properties from a "blueprint" service.
     *
     * The service to be merged does not nessessarily need to be a blueprint
     * itself.
     *
     * @param ServiceInterface $bp
     *
     * @return void
     */
    public function mergeFromBluePrint(ServiceInterface $bp);

    /**
     * calls
     *
     * @param CallerReferenceInterface $caller
     *
     * @return void
     */
    public function calls(CallerReferenceInterface $caller);

    /**
     * getCallers
     *
     * @return array
     */
    public function getCallers();

    /**
     * Acts like calls, but instead calls the given method on the service object itself.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return void
     */
    public function sets($method, array $arguments = []);

    /**
     * Sets setter methods and arguments.
     *
     * @param array $setters
     *
     * @return void
     */
    public function setSetters(array $setters);

    /**
     * Get setter methods and arguments.
     *
     * @return array
     */
    public function getSetters();

    /**
     * Marks this services as injected into the DIC, and therefor not
     * createable by the DIC.
     *
     * @param bool $injected
     *
     * @return void
     */
    public function setInjected($injected);

    /**
     * isInjected
     *
     * @return bool
     */
    public function isInjected();

    /**
     * Binds a service to another one, e.g for setter or constructor injection.
     *
     * Bound services cannot be received by the DIC or from services they're
     * not bound to.
     *
     * @param ServiceReferenceInterface $binding
     *
     * @return void
     */
    public function addBinding(ServiceReferenceInterface $binding);

    /**
     * Checks if this service is bound the given one.
     *
     * @param string|ServiceReferenceInterface $binding
     *
     * @return void
     */
    public function isBoundTo($binding);

    /**
     * Checks if this service is bound to any service.
     *
     * @return bool
     */
    public function isBound();
}
