<?php

/*
 * This File is part of the Lucid\Module\Template\Function package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Extension;

/**
 * @interface FunctionInterface
 *
 * @package Lucid\Module\Template\Function
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface FunctionInterface
{

    /**
     * Calls the callable with given arguments
     *
     * @param array $arguments
     *
     * @return mixed
     */
    public function call(array $arguments = []);

    /**
     * Get the funtion name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the function callable.
     *
     * @return callable
     */
    public function getCallable();

    /**
     * Get the options.
     *
     * @return array
     */
    public function getOptions();

    /**
     * Returns the function alias.
     *
     * @return void
     */
    public function __toString();

    /**
     * Calls the callable.
     *
     * @return mixed
     */
    public function __invoke();
}
