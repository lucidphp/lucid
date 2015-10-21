<?php

/*
 * This File is part of the Lucid\Event package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Event;

/**
 * @interface ChainedEventInterface
 *
 * @package Lucid\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ChainedEventInterface extends EventInterface
{
    /**
     * setDispatcher
     *
     * @param EventDispatcherInterface $dispatcher
     *
     * @return void
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher);

    /**
     * getDispatcher
     *
     * @return EventDispatcherInterface|null
     */
    public function getDispatcher();
}
