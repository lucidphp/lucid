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

use Lucid\DI\Reference\ServiceInterface as Reference;

/**
 * @class Graph
 *
 * @package Lucid\DI\Dependency
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Graph
{
    public function __construct()
    {
        $this->nodes = [];
    }

    public function generate(array $defs)
    {
        $nodes = array_map(function ($id) {
            return new Node($id);
        }, array_keys($defs));

        $nodes = array_combine(array_keys($defs), $nodes);

        $vertices = [];

        foreach ($defs as $id => $def) {
            foreach ($def->getArguments() as $arg) {
                if ($arg instanceof Reference) {
                    $nodes[$id]->connect($nodes[(string)$arg]);
                }
            }
        }
    }

    private function initNodes($id)
    {
        return new Node($id);
    }
}
