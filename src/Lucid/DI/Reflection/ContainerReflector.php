<?php

/*
 * This File is part of the Lucid\DI\Reflection package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Reflection;

use SplFixedArray;

/**
 * @class ContainerReflector
 *
 * @package Lucid\DI\Reflection
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerReflector implements ContainerTargetInterface
{
    public function __construct($baseClass, $targetClass)
    {
        $this->baseClass = $baseClass;
        $this->targetClass = $targetClass;
    }

    public function reflect(ContainerBuilderInterface $container)
    {

    }

    private function parseTree(ContainerBuilderInterface $container)
    {
        $methods = $this->getMethods($container);
    }


    private function getMethods(ContainerBuilderInterface $container)
    {
        $methods = new SplFixedArray(sizeof($keys = array_keys($container->getServices())));

        foreach ($keys as $id) {
            $methods[] = new ContainerReflectionMethod($id, $container, ContainerReflectionMethod::T_PROTECTED);
        }

        return $methods;
    }
}
