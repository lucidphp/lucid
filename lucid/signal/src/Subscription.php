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
 * @class Subscription
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Subscription implements SubscriptionInterface
{
    /** @var array */
    private $subscriptions;

    /**
     * Constructor.
     *
     * @param array $subscriptions
     */
    public function __construct(array $subscriptions)
    {
        $this->subscriptions = $subscriptions;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        foreach ($this->subscriptions as $e => $sub) {
            yield $e => $sub;
        }
    }
}
