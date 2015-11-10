<?php

/*
 * This File is part of the Lucid\Signal package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Signal;

/**
 * @class HandlerInterface
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface HandlerInterface
{
    /**
     * Will be called on a specific event.
     *
     * @param EventInterface $event the event that's been dispatched.
     *
     * @return void
     */
    public function handleEvent(EventInterface $event);
}
