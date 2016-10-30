<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
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
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface TypeMapCollectionInterface
{
    /**
     * Sets an array of typemapper objects.
     *
     * @param TypeMapperInterface[] $typeMappers
     *
     * @return void
     */
    public function set(array $typeMappers) : void;

    /**
     * Add a typemapper object to the collection.
     *
     * @param TypeMapperInterface $typeMapper
     *
     * @return void
     */
    public function add(TypeMapperInterface $typeMapper) : void;

    /**
     * Check if a mapper for a given type exists.
     *
     * @param string $type
     *
     * @return bool
     */
    public function has(string $type) : bool;

    /**
     * Get the object for a given type.
     *
     * @param string $type
     *
     * @return Object
     */
    public function get(string $type);

    /**
     * Get the mapper for a given type.
     *
     * @param string $type
     *
     * @return TypeMapperInterface
     */
    public function getMapper(string $type) : ?TypeMapperInterface;
}
