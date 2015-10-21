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

/**
 * @interface ResourceCollectionInterface
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResourceCollectionInterface
{
    public function addResource(ResourceInterface $resource);

    /**
     * Add a file resource.
     *
     * @param string $file
     *
     * @return void
     */
    public function addFileResource($file);

    /**
     * Add a object resource.
     *
     * @param object $object
     *
     * @return void
     */
    public function addObjectResource($object);

    /**
     * Get all resources as array
     *
     * @return ResourceInterface[]
     */
    public function all();

    /**
     * isValid
     *
     * @param int $timestamp
     *
     * @return boolean
     */
    public function isValid($timestamp);
}
