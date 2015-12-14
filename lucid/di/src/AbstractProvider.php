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
    const FACTORY_PREFIX = 'getService';

    /** @var array */
    const FACTORY_REPLACEMENT = [
        '_' => ' ', '-' => 'Sl ', ':' => 'Cl ',
        '.' => 'Dt ', '\\' => 'Ns '
    ];

    /** @var array */
    private $cmap;

    /**
     * Constructor
     *
     * @param array $cmap
     */
    final public function __construct(array $cmap = [])
    {
        $this->cmap = $cmap;
    }

    /**
     * {@inheritdoc}
     */
    final public function provides($service)
    {
        if (isset($this->cmap[$service])) {
            return true;
        }

        if (method_exists($this, $method = $this->getFactoryName($service))) {
            $this->cmap[$service] = $method;

            return true;
        }

        return false;
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
     * {@inheritdoc}
     */
    final public function provide($service)
    {
        try {
            return call_user_func([$this, $this->getFactoryName($service)], $service);
        } catch (BadMethodCallException $e) {
        }

        return null;
    }

    /**
     * @return string
     */
    final public static function factoryName($id)
    {
        return static::FACTORY_PREFIX.self::camalizeId($id);
    }

    /**
     * @return string
     */
    final public static function camalizeId($id)
    {
        return strtr(ucwords(strtr($id, static::FACTORY_REPLACEMENT)), [' ' => '']);
    }

    /**
     * getFactoryName
     *
     * @param string $id
     *
     * @return string
     */
    private function getFactoryName($service)
    {
        return isset($this->cmap[$service]) ? $this->cmap[$service] : self::factoryName($service);
    }
}
