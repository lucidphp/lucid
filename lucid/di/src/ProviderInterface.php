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

/**
 * @interface ProviderInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ProviderInterface
{
    /**
     * Tells if it can create or provide a serice.
     *
     * @param string $service
     *
     * @return bool
     */
    public function provides($service);

    /**
     * Provides a service.
     *
     * @param string $serice
     *
     * @return Object
     */
    public function provide($serice);
}
