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

use Lucid\Common\Helper\Str;
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
    public $aliases;
    protected $parameters;
    protected $instances;
    protected $synced;
    protected $cmap;
    protected $icmap;

    /**
     * Constructor.
     *
     * @param ParameterInterface $parameters
     * @param array $aliases alias map
     * @param array $cmap constructor map
     * @param array $icmap
     * @param array $synced synced map
     */
    public function __construct(
        ParameterInterface $params = null,
        array $aliases = [],
        array $cmap = [],
        array $icmap = [],
        array $synced = []
    ) {
        $this->parameters = $params ?: new Parameters;
        $this->aliases    = $aliases;
        $this->cmap       = $cmap;
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
            throw new ContainerException;
        }

        $this->instances[$id] = $object;
    }

    /**
     * {@inheritdoc}
     */
    public function replace($id, $object, $behaves = self::EXCEPTION_ON_MISSING)
    {
        if (self::EXCEPTION_ON_MISSING === $behaves && !$this->has($id)) {
            throw new ContainerException;
        }

        $this->instances[$id] = $object;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(sprintf('service %s could not be found on this container.', $id));
        }

        $id = $this->getAlias($id);

        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        return call_user_func([$this, $this->getFactoryName($id)], $id);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        $id = $this->getAlias($id);

        return array_key_exists($id, $this->instances) || $this->hasMethod($id);
    }

    /**
     * {@inheritdoc}
     */
    public function syncronize($id, $caller)
    {
    }

    /**
     * getAlias
     *
     * @param string $alias
     *
     * @return string
     */
    public function getAlias($alias)
    {
        return isset($this->aliases[$alias]) ? $this->aliases[$alias] : $alias;
    }

    /**
     * setAlias
     *
     * @param mixed $id
     * @param mixed $alias
     *
     * @return void
     */
    public function setAlias($id, $alias)
    {
        if (0 === strcmp($id, $alias)) {
            throw ContainerException::curcularReference();
        }

        $this->aliases[$alias] = $id;
    }

    /**
     * hasMethod
     *
     * @param string $id
     *
     * @return boolean
     */
    protected function hasMethod($id)
    {
        if (isset($this->cmap[$id])) {
            return true;
        }

        if (method_exists($this, $method = static::factoryName($id))) {
            $this->cmap[$id] = $method;

            return true;
        }

        return false;
    }

    /**
     * getFactoryName
     *
     * @param string $id
     *
     * @return string
     */
    protected function getFactoryName($id)
    {
        return isset($this->cmap[$id]) ? $this->cmap[$id] : static::factoryName($id);
    }

    /**
     * callConstructor
     *
     * @param string $id
     *
     * @return object
     */
    protected function callConstructor($id, array $map)
    {
        return call_user_func([$this, $map[$this->getIdFromAlias($id)]]);
    }

    /**
     * @return string
     */
    public static function factoryName($id)
    {
        return 'getService'.self::camalizeId($id);
    }

    /**
     * @return string
     */
    public static function camalizeId($id)
    {
        return Str::camelCaseAll(
            $id,
            ['_' => ' ', '-' => 'Sl ', ':' => 'Cl ', '.' => 'Dt ', '\\' => 'Ns ']
        );
    }

    /**
     * __set
     *
     * @param string $param
     * @param mixed $value
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

    private static function getReadablyProps()
    {
        return ['parameters', 'aliases'];
    }
}
