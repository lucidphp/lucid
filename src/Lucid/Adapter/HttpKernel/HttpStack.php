<?php

/**
 * This File is part of the Selene\Adapter\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

/**
 * @class Stack implements HttpKernelInterface, TerminableInterface
 * @see HttpKernelInterface
 * @see TerminableInterface
 *
 * @package Selene\Adapter\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class HttpStack implements HttpKernelInterface, TerminableInterface
{
    /**
     * kernel
     *
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $kernel;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $app
     */
    public function __construct(HttpKernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * handleRequest
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $type
     * @param boolean $catch
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        return $this->getKernel()->handle($request, $type, $catch);
    }

    /**
     * terminate
     *
     * @param \Symfony\Component\HttpFoundation\Request  $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        if (($kernel = $this->getKernel()) instanceof TerminableInterface) {
            $kernel->terminate($request, $response);
        } elseif ($kernel->getKernel() instanceof TerminableInterface) {
            $kernel->getKernel()->terminate($request, $response);
        }
    }

    /**
     * getKernel
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected function getKernel()
    {
        return $this->kernel;
    }
}
