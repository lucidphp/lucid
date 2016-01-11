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

use Iterator;

/**
 * @interface CollectionInterface
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CollectionInterface extends Iterator
{
    /**
     * Adds a resource.
     *
     * @param ResourceInterface $resource
     *
     * @return void
     */
    public function addResource(ResourceInterface $resource);

    /**
     * Adds a file resource.
     *
     * @param string $file
     *
     * @return void
     */
    public function addFileResource($file);

    /**
     * Adds a object resource.
     *
     * @param object $object
     *
     * @return void
     */
    public function addObjectResource($object);

    /**
     * Gets all resources as array.
     *
     * @return ResourceInterface[]
     */
    public function all();

    /**
     * Checks if all resources are still valid.
     *
     * @param int $timestamp
     *
     * @return bool
     */
    public function isValid($timestamp);
}
