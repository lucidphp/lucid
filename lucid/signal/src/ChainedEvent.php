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
 * Class ChainedEvent
 * @package Lucid\Signal
 */
class ChainedEvent extends Event implements ChainedEventInterface
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    final public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    final public function getDispatcher() : ?EventDispatcherInterface
    {
        return $this->dispatcher;
    }
}
