<?php

/*
 * This File is part of the Lucid\DI\Reference package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Reference;

/**
 * @class CallerReference
 *
 * @package Lucid\DI\Reference
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CallerReferenceInterface
{
    /**
     * Get the referenced service;
     *
     * @return ServiceReferenceInterface
     */
    public function getService();

    /**
     * Get the method to be called
     *
     * @return string
     */
    public function getMethod();

    /**
     * Get the caller arguments.
     *
     * @return array
     */
    public function getArguments();
}
