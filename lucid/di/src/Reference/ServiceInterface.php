<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Reference;

/**
 * @interface ServiceReferenceInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ServiceInterface
{
    /**
     * Gets the service id.
     *
     * @return string
     */
    public function getId();

    /**
     * Should call getId.
     *
     * @return string
     */
    public function __toString();
}
