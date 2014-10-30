<?php

/*
 * This File is part of the Lucid\Module\Routing\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Handler;

/**
 * @class TypeMapCollection
 *
 * @package Lucid\Module\Routing\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TypeMapCollection implements TypeMapCollectionInterface
{
    public function __construct(array $mappers = [])
    {
        $this->set($mappers);
    }

    public function set(array $mappers)
    {
        $this->mappers = [];

        foreach ($mappers as $mapper) {
            $this->add($mapper);
        }
    }

    public function add(TypeMapperInterface $mapper)
    {
        $this->mappers[$this->sanitize($mapper->getType())] = $mapper;
    }

    public function has($type)
    {
        return isset($this->mappers[$this->sanitize($type)]);
    }

    public function get($type)
    {
        return $this->getMapper($type)->getObject();
    }

    public function getMapper($type)
    {
        if ($this->has($type)) {
            return $this->mappers[$this->sanitize($type)];
        }
    }


    private function sanitize($type)
    {
        return '\\'.ltrim($type, '\\');
    }
}
