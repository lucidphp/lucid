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
 * @class TypeMapCollection
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class TypeMapCollection implements TypeMapCollectionInterface
{
    /** @var TypeMapperInterface[] */
    private $mappers;

    /**
     * TypeMapCollection constructor.
     *
     * @param TypeMapperInterface[] $mappers
     */
    public function __construct($mappers = [])
    {
        $this->doSet(...$mappers);
    }

    /**
     * {@inheritdoc}
     */
    public function set(array $mappers) : void
    {
        $this->doSet(...$mappers);
    }

    /**
     * {@inheritdoc}
     */
    public function add(TypeMapperInterface $mapper) : void
    {
        $this->mappers[$this->sanitize($mapper->getType())] = $mapper;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $type) : bool
    {
        return isset($this->mappers[$this->sanitize($type)]);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $type)
    {
        return $this->getMapper($type)->getObject();
    }

    /**
     * {@inheritdoc}
     */
    public function getMapper(string $type) : ?TypeMapperInterface
    {
        if (!$this->has($type)) {
            return null;
        }

        return $this->mappers[$this->sanitize($type)];
    }

    /**
     * Sanitize class names.
     *
     * @param string $type
     *
     * @return string
     */
    private function sanitize($type)
    {
        return '\\'.ltrim($type, '\\');
    }

    /**
     * @param \Lucid\Mux\Handler\TypeMapperInterface[] ...$mappers
     */
    private function doSet(TypeMapperInterface ...$mappers) : void
    {
        $this->mappers = array_combine(array_map(function (TypeMapperInterface $mapper) {
            return $this->sanitize($mapper->getType());
        }, $mappers), $mappers);
    }
}
