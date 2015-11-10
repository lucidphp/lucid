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
 * @class Priority
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Priority
{
    public function add($handler, $priority)
    {
        $this->stach[$priority][] = $handler;
        $this->lut[$this->getHandlerString($handler)] = [$priority];
    }

    public function remove($handler)
    {
        $str = $this->getHandlerString
        if ($this->has($handler)) {
        }

    }

    private function getHandlerString()
    {
        if (is_string($handler)) {
            return $handler;
        }

        if (is_object($handler)) {
            return spl_object_hash($handler);
        }
    }
}
