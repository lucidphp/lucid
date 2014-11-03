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

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Lucid\Module\Common\DataTypes\ReversePriorityQueue;

/**
 * @class StackBuilder
 *
 * @package Selene\Adapter\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class HttpStackBuilder
{
    /**
     * app
     *
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    private $app;

    /**
     * stack
     *
     * @var \SplPriorityQueue
     */
    private $queue;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $app
     * @param array $kernels
     */
    public function __construct(HttpKernelInterface $kernel, array $kernels = [])
    {
        $this->kernel = $kernel;
        $this->set($kernels);
    }

    /**
     * Set kernels.
     *
     * @param array $kernels
     *
     * @return void
     */
    public function set(array $kernels)
    {
        $this->queue = new ReversePriorityQueue;

        foreach ($kernels as $kernel) {
            $this->add($kernel);
        }
    }

    /**
     * Add a kernel to the stack.
     *
     * @param StackedKernelInterface $kernel
     *
     * @return void
     */
    public function add(Middleware $kernel)
    {
        $this->queue->insert($kernel, $kernel->getPriority());
    }

    /**
     * Creates new stacked kernel.
     *
     * @param AppCoreInterface $app
     *
     * @return Stack
     */
    public function make()
    {
        $app = $this->kernel;

        while ($this->queue->valid()) {
            $kernel = $this->queue->extract();
            $kernel->setKernel($app);
            $app = $kernel;
        }

        return new HttpStack($app);
    }
}
