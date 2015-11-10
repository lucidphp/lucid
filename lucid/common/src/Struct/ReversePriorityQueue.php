<?php

/*
 * This File is part of the Lucid\Common package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Common\Struct;

/**
 * @class ReversePriorityQueue
 * @see PriorityQueue
 *
 * @package Lucid\Common
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ReversePriorityQueue extends PriorityQueue
{
    /**
     * {@inheritdoc}
     */
    public function compare($prio1, $prio2)
    {
        list($p1, $order1) = (array)$prio1;
        list($p2, $order2) = (array)$prio2;

        if ($p1 === $p2) {
            return (int)$order1 > (int)$order2 ? 1 : -1;
        }

        return $p1 < $p2 ? 1 : -1;
    }
}
