<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Handler;

use Lucid\Mux\Matcher\ContextInterface;

/**
 * @interface DispatcherInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface DispatcherInterface
{
    /**
     * Dispatches a handler from a match context.
     *
     * @param ContextInterface $context
     *
     * @return void
     */
    public function dispatch(ContextInterface $context);
}
