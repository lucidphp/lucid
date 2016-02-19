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
 * @class Node
 *
 * @package Lucid\DI\Dependency
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Node
{
    /** @var string */
    public $name;

    /** @var Node */
    public $parent;

    /** @var array */
    public $vertices = [];

    /**
     * Constructor
     *
     * @param string $name
     * @param Node $parent
     */
    public function __construct($name, Node $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    public function addChild(Node $child)
    {
        $this->children[$child->name] = new Vertix($child);
    }

    public function connect(Node $child)
    {
        $this->vertices[] = new Vertix($this, $child);
    }
}
