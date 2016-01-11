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

use IteratorAggregate;

/**
 * @class Collection
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Collection implements CollectionInterface
{
    /** @var int */
    private $current = 0;

    /** @var ResourceInterface[] */
    private $resources;

    /**
     * Constructor.
     *
     * @param array $resources
     */
    public function __construct(array $resources = [])
    {
        $this->setResources($resources);
    }

    /**
     * setResources
     *
     * @param array $resources
     *
     * @return void
     */
    public function setResources(array $resources)
    {
        $this->resources = [];

        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }


    /**
     * {@inheritdoc}
     */
    public function addFileResource($file)
    {
        $this->addResource(new FileResource($file));
    }

    /**
     * {@inheritdoc}
     */
    public function addObjectResource($object)
    {
        $this->addResource(new ObjectResource($object));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->resources;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($timestamp)
    {
        foreach ($this->resources as $resource) {
            if (!$resource->isValid($timestamp)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->resources[$this->current];
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->current++;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->current = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->current < count($this->resources);
    }
}
