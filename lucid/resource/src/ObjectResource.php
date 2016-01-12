<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource;

use LogicException;
use ReflectionObject;
use InvalidArgumentException;

/**
 * @class ObjectResource
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ObjectResource extends AbstractResource
{
    /**
     * Constructor.
     *
     * @param object $object
     */
    public function __construct($object)
    {
        $this->resource = $this->getObjectResource($object);
    }

    /**
     * getObjectResource
     *
     * @param mixed $object
     *
     * @return string
     */
    private function getObjectResource($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException(
                sprintf('%s expects first argument to be object, instead saw %s', get_class($this), gettype($object))
            );
        }

        $reflection = new ReflectionObject($object);

        if ($reflection->isInternal()) {
            throw new LogicException(
                sprintf('Cannot use internal class "%s" as resource.', $reflection->getName())
            );
        }

        return $reflection->getFileName();
    }
}
