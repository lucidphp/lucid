<?php

/*
 * This File is part of the Lucid\DI\Dependency package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Dependency;

/**
 * @class Vertix
 *
 * @package Lucid\DI\Dependency
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Vertix
{
    /** @var Node */
    public $start;

    /** @var Node */
    public $end;

    /**
     * Constructor.
     *
     * @param Node $a
     * @param Node $b
     */
    public function __construct(Node $start, Node $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }
}
