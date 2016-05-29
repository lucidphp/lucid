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
    /** @var int */
    private $queueOrder = PHP_INT_MAX;

    /**
     * {@inheritdoc}
     */
    public function insert($data, $priority)
    {
        if (is_int($priority)) {
            $priority = [$priority, $this->queueOrder--];
        }

        parent::insert($data, $priority);
    }
}
