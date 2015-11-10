<?php

/*
 * This File is part of the Lucid\Common\Data package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Struct;

use SplPriorityQueue;

/**
 * @class PriorityQueue
 * @see \SplPriorityQueue
 *
 * @package Lucid\Common\Data
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PriorityQueue extends SplPriorityQueue
{
    /**
     * queueOrder
     *
     * @var int
     */
    private $queueOrder;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->queueOrder = PHP_INT_MAX;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($datum, $priority)
    {
        if (is_int($priority)) {
            $priority = [$priority, $this->queueOrder--];
        }

        parent::insert($datum, $priority);
    }
}
