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
 * @class Event
 *
 * @package Lucid\Signal
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Event implements EventInterface
{
    use EventTrait;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name = null)
    {
        $this->originalName = new EventName($this, (string)$name);
    }
}
