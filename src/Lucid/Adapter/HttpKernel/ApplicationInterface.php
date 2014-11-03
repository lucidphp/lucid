<?php

/*
 * This File is part of the Lucid\Adapter\HttpKernel package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

/**
 * @class ApplicationInterface
 *
 * @package Lucid\Adapter\HttpKernel
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ApplicationInterface extends HttpKernelInterface, TerminableInterface
{
    /**
     * Run the application
     *
     * @return int
     */
    public function run(Request $request = null, $catch = null);
}
