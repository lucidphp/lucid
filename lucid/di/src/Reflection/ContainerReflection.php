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

/**
 * @class ContainerReflection
 *
 * @package Lucid\DI\Reflection
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerReflection
{
    public function __construct($baseClass, $targetClass)
    {
        $this->baseClass = $baseClass;
        $this->targetClass = $targetClass;
    }
}
