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
 * @class SubscriberInterface
 *
 * @package Lucid\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SubscriberInterface
{
    /**
     * Get event subscriptions.
     *
     * @return array Array of event subscriptions
     */
    public function getSubscriptions();
}
