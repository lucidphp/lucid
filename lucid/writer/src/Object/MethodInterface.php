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
    /**
     * Sets the methods return type.
     *
     * @param string $type
     *
     * @return void
     */
    public function setType($type);

    /**
     * Returns the name of the method.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the methods arguments.
     *
     * @param array $arguments a list of `Argument`
     *
     * @return void
     */
    public function setArguments(array $arguments);

    /**
     * Adds an argument.
     *
     * @param Argument $argument
     *
     * @return void
     */
    public function addArgument(Argument $argument);
}
