<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Object;

use Lucid\Writer\GeneratorInterface;

/**
 * @interface MethodInterface
 * @see GeneratorInterface
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface MethodInterface extends GeneratorInterface
{
    public function setType($type);

    public function getName();

    public function setArguments(array $arguments);

    public function addArgument(Argument $argument);
}
