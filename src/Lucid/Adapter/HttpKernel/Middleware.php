<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel;

use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @interface Middleware
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface Middleware extends HttpKernelInterface
{
    const PRIORITY_NORMAL = 0;
    const PRIORITY_HIGH = 1000;
    const PRIORITY_LOW = -1000;

    /**
     * Set the main kernel.
     *
     * @param HttpKernelInterface $kernel
     *
     * @return void
     */
    public function setKernel(HttpKernelInterface $kernel);

    /**
     * Get the main kernel
     *
     * @return HttpKernelInterface
     */
    public function getKernel();

    /**
     * Get the priority
     *
     * @return int
     */
    public function getPriority();
}
