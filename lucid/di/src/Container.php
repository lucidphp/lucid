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
use Interop\Container\ContainerInterface as InteropContainer;

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
    protected $synced;

    /** @var array */
    protected $icmap;

    /** @var array */
    protected $aliases;

    /** @var array `InteropContainer[]` */
    private $containers = [];

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
        array $synced = []
    ) {
        $this->provider   = $provider ?: new SimpleProvider;
        $this->parameters = $params ?: new Parameters;
        $this->aliases    = $aliases;
        $this->synced     = $synced;
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $object, $forceReplace = self::EXCEPTION_ON_DUPLICATE)
    {
        if (self::EXCEPTION_ON_DUPLICATE === $forceReplace && $this->has($id)) {
            throw new ContainerException(
                sprintf(
                    'Service "%s" is alread set. Use "%s::replace()", or pass "$forceReplace".',
                    $id,
                    get_class($this)
                )
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

        if ($instance = $this->provider->getInstance($id)) {
            return $instance;
        }

        if ($instance = $this->provider->provide($id)) {
            return $instance;
        }

        return $this->delegateGet($id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->provider->provides($id = $this->getId($id)) || $this->delegateHas($id);
    }

    /**
     * {@inheritdoc}
     */
    public function delegate(InteropContainer $container)
    {
        $this->containers[] = $container;
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
    public function __get($thing)
    {
        if (in_array($thing, static::getReadableProps())) {
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
            throw new InvalidArgumentException(
                sprintf('Serice must be of type object, instead saw "%s".', gettype($object))
            );
        }

        $this->provider->setInstance($id, $object);
    }

    /**
     * delegateHas
     *
     * @param string $id
     *
     * @return bool
     */
    private function delegateHas($id)
    {
        if (empty($this->containers)) {
            return false;
        }

        foreach ($this->containers as $container) {
            if ($container->has($id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * delegateGet
     *
     * @param string $id
     *
     * @return object
     */
    private function delegateGet($id)
    {
        if (empty($this->containers)) {
            return;
        }

        foreach ($this->containers as $container) {
            if ($thing = $container->get($id)) {
                return $thing;
            }
        }
    }

    /**
     * getReadablyProps
     *
     * @return array
     */
    private static function getReadableProps()
    {
        return ['parameters', 'aliases'];
    }
}
