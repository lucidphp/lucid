<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI;

use InvalidArgumentException;
use Lucid\Config\Parameters;
use Lucid\Config\ParameterInterface;
use Lucid\DI\Exception\NotFoundException;
use Lucid\DI\Exception\ContainerException;

/**
 * @class Container
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Container implements ContainerInterface
{
    /** @var int */
    const FACTORY_NOT_FOUND = -1;

    /** @var ProviderInterface */
    protected $provider;

    /** @var ParameterInterface */
    protected $parameters;

    /** @var array */
    protected $instances;

    /** @var array */
    protected $synced;

    /** @var array */
    protected $icmap;

    /** @var array */
    protected $aliases;

    /**
     * Constructor.
     *
     * @param ParameterInterface $parameters
     * @param array $aliases alias map
     * @param array $cmap constructor map
     * @param array $icmap map of internal services
     * @param array $synced synced map of synchroniued services
     */
    public function __construct(
        ProviderInterface $provider = null,
        ParameterInterface $params = null,
        array $aliases = [],
        array $icmap = [],
        array $synced = []
    ) {
        $this->provider   = $provider;
        $this->parameters = $params ?: new Parameters;
        $this->aliases    = $aliases;
        $this->icmap      = $icmap;
        $this->synced     = $synced;
        $this->instances  = [];
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $object, $forceReplace = self::EXCEPTION_ON_DUPLICATE)
    {
        if (self::EXCEPTION_ON_DUPLICATE === $forceReplace && $this->has($id)) {
            throw new ContainerException(
                sprintf('Service "%s" is alread set. Use "%s::replace()", or pass "$forceReplace".', $id, get_class($this))
            );
        }

        $this->doSet($id, $object);
    }

    /**
     * {@inheritdoc}
     */
    public function replace($id, $object, $behaves = self::EXCEPTION_ON_MISSING)
    {
        if (self::EXCEPTION_ON_MISSING === $behaves && !$this->has($id)) {
            throw new ContainerException(sprintf('Service "%s" cannot be replaced, as it doesn\'t exist.', $id));
        }

        $this->doSet($id, $object);
    }

    /**
     * {@inheritdoc}
     */
    public function setAlias($id, $alias)
    {
        if (0 === strcmp($id, $alias)) {
            throw ContainerException::curcularReference();
        }

        $this->aliases[$alias] = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId($alias)
    {
        return isset($this->aliases[$alias]) ? $this->aliases[$alias] : $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(sprintf('Service "%s" could not be found on this container.', $id));
        }

        $id = $this->getId($id);

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        return $this->provider->provide($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        $id = $this->getId($id);

        return array_key_exists($id, $this->instances) || (null !== $this->provider && $this->provider->provides($id));
    }

    /**
     * Don't set anything.
     *
     * @param string $param
     * @param mixed $value
     *
     * @throws ContainerException
     *
     * @return void
     */
    public function __set($param, $value)
    {
        throw new ContainerException('Cannot set READ ONLY properties.');
    }

    /**
     * __get
     *
     * @param mixed $param
     *
     * @return void
     */
    public function __get($param)
    {
        if (in_array($param, static::getReabableProps())) {
            return $this->param;
        }

        throw new ContainerException('Property is undefined or READ ONLY property.');
    }

    /**
     * doSet
     *
     * @param string $id
     * @param object $object
     *
     * @return void
     */
    private function doSet($id, $object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException(sprintf('Serice must be of type object, instead saw "%s".', gettype($object)));
        }

        $this->instances[$id] = $object;
    }

    /**
     * getReadablyProps
     *
     * @return array
     */
    private static function getReadablyProps()
    {
        return ['parameters', 'aliases'];
    }
}
