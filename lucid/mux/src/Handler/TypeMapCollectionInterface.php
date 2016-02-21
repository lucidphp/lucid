<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Handler;

/**
 * @interface TypeMapCollectionInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface TypeMapCollectionInterface
{
    /**
     * Sets an array of typemapper objects.
     *
     * @param array $typeMappers
     *
     * @return void
     */
    public function set(array $typeMappers);

    /**
     * Add a typemapper object to the collection.
     *
     * @param TypeMapperInterface $typeMapper
     *
     * @return void
     */
    public function add(TypeMapperInterface $typeMapper);

    /**
     * Check if a mapper for a given type exists.
     *
     * @param string $type
     *
     * @return boolean
     */
    public function has($type);

    /**
     * Get the object for a given type.
     *
     * @param string $type
     *
     * @return Object
     */
    public function get($type);

    /**
     * Get the mapper for a given type.
     *
     * @param string $type
     *
     * @return TypeMapperInterface
     */
    public function getMapper($type);
}
