<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http;

use SplStack;
use Countable;

/**
 * @class RequestStack
 * @see RequestStackInterface
 * @see Countable
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RequestStack implements RequestStackInterface, Countable
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->stack = new SplStack;
    }

    /**
     * {@inheritdoc}
     */
    public function push(RequestInterface $request)
    {
        return $this->stack->push($request);
    }

    /**
     * {@inheritdoc}
     */
    public function pop()
    {
        return $this->stack->pop();
    }

    /**
     * {@inheritdoc}
     */
    public function getMain()
    {
        if ($this->isEmpty()) {
            return null;
        }

        return $this->stack->bottom();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrent()
    {
        return $this->isEmpty() ? null : $this->stack->top();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrevious()
    {
        if (0 === ($count = $this->stack->count()) || $count < 2) {
            return null;
        }

        return $this->stack[$count - ($count - 1)];
    }

    /**
     * Get the size of the stack
     *
     * @return int
     */
    public function count()
    {
        return $this->stack->count();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return 0 === $this->stack->count();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll()
    {
        // stack->valid won't work.
        while ($this->count()) {
            $this->stack->pop();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubRequests()
    {
        if ($req = $this->getMain()) {
            $this->removeAll();
            $this->stack->push($req);
        }
    }
}
