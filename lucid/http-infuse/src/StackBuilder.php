<?php

/*
 * This File is part of the Lucid\Http\Infuse package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Infuse;

use Lucid\Http\Core\DispatcherInterface;
use Lucid\Common\Struct\ReversePriorityQueue;

/**
 * @class StackBuilder
 *
 * @package lucid/http-infuse
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class StackBuilder
{
    /** @var ReversePriorityQueue */
    private $queue;

    /** @var DispatcherInterface */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param DispatcherInterface $dispatcher
     * @param array $middlewares
     */
    public function __construct(DispatcherInterface $dispatcher, array $middlewares = [])
    {
        $this->dispatcher = $dispatcher;
        $this->queue      = new ReversePriorityQueue;

        foreach ($middlewares as $middleware) {
            $this->add($middleware);
        }
    }

    /**
     * Add a http middleware to the stack.
     *
     * @param MiddlewareInterface $kernel
     * @param int $priority
     *
     * @return void
     */
    public function add(MiddlewareInterface $middleware, $priority = null)
    {
        $this->queue->insert($middleware, null !== $priority ? $priority : $middleware->getPriority());
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
        $main =& $this->dispatcher;

        while ($this->queue->valid()) {
            $dispatcher = $this->queue->extract();
            $dispatcher->setDispatcher($main);

            // set $main reference to the current middleware
            $main =& $dispatcher;
        }

        return new Stack($main);
    }
}
