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

use BadMethodCallException;
use Lucid\Common\Helper\Str;

/**
 * @class ServiceFactory
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractProvider implements ProviderInterface
{
    /** @var string */
    const FACTORY_PREFIX          = 'getService';

    /** @var string */
    const FACTORY_PREFIX_INTERNAL = 'getInternalService';

    /** @var array */
    const FACTORY_REPLACEMENT = [
        '_' => ' ', '-' => 'Sl ', ':' => 'Cl ',
        '.' => 'Dt ', '\\' => 'Ns '
    ];

    /** @var array */
    protected $cmap;

    /** @var array */
    protected $params;

    /** @var array */
    protected $internals;

    /** @var array */
    protected $instances = [];

    /**
     * Constructor
     *
     * @param array $cmap
     * @param array $params
     * @param array $internals
     */
    public function __construct(array $cmap = [], array $params = [], array $internals = [])
    {
        $this->cmap      = $cmap;
        $this->params    = $params;
        $this->internals = $internals;
    }

    /**
     * {@inheritdoc}
     */
    final public function provides($service)
    {
        if (isset($this->cmap[$service]) || isset($this->instances[$service])) {
            return true;
        }

        if (method_exists($this, $method = $this->getFactoryName($service))) {
            $this->cmap[$service] = $method;

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    final public function provide($service)
    {
        try {
            return call_user_func([$this, $this->getFactoryName($service)], $service, false);
        } catch (BadMethodCallException $e) {
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    final public function getInstance($id)
    {
        return isset($this->instances[$id]) ? $this->instances[$id] : null;
    }

    /**
     * {@inheritdoc}
     */
    final public function setInstance($id, $instance)
    {
        $this->instances[$id] = $instance;
    }

    /**
     * Returns an internal service.
     *
     * @param string $service
     *
     * @return object
     */
    protected function getInternal($service)
    {
        try {
            return call_user_func([$this, $this->getFactoryName($service)], $service, true);
        } catch (BadMethodCallException $e) {
        }

        return null;
    }

    /**
     * Throws a BadMethodCallException
     *
     * @param string $method
     * @param mixed $args
     * @throws BadMethodCallException
     *
     * @return void
     */
    final public function __call($method, $args)
    {
        throw new BadMethodCallException('Service not available.');
    }


    /**
     * @return string
     */
    final public static function factoryName($id, $prefix = self::FACTORY_PREFIX)
    {
        return $prefix.self::camalizeId($id);
    }

    /**
     * @return string
     */
    final public static function camalizeId($id)
    {
        return strtr(ucwords(strtr($id, static::FACTORY_REPLACEMENT)), [' ' => '']);
    }

    /**
     * getParame
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getParame($key)
    {
        return array_key_exists($key, $this->params) ? $this->params[$key] : null;
    }

    /**
     * getFactoryName
     *
     * @param string $id
     *
     * @return string
     */
    private function getFactoryName($service, $internal = false)
    {
        $prefix = $internal ? self::FACTORY_PREFIX_INTERNAL : self::FACTORY_PREFIX;
        var_dump($prefix);
        return isset($this->cmap[$service]) ?
            $this->cmap[$service] :
            self::factoryName($service, $prefix);
    }
}
