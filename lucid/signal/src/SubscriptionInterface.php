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

use Iterator;

/**
 * @interface SubscriptionInterface
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SubscriptionInterface
{
    /**
     * Returns an iteratable list of subscriptions.
     *
     * @return Iterator
     */
    public function get() : Iterator;
}
