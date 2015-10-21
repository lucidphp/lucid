<?php

/*
 * This File is part of the Lucid\Config package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Config;

use Lucid\Config\Exception\ParameterException;

/**
 * @class StaticParameters
 * @see ParameterInterface
 *
 * @package Lucid\Config
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class StaticParameters implements ParameterInterface
{
    /**
     * parameters
     *
     * @var array
     */
    private $parameters;

    /**
     * Constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     * @throws ParameterException always
     */
    public function set($param, $value)
    {
        throw ParameterException::lockedCall('set');
    }

    /**
     * get
     *
     * @param mixed $param
     *
     * @return mixed
     */
    public function get($param)
    {
        if ($this->has($param)) {
            return $this->parameters[strtolower($param)];
        }

        throw ParameterException::undefinedParameter($param);
    }

    /**
     * has
     *
     * @param mixed $param
     *
     * @return mixed
     */
    public function has($param)
    {
        return array_key_exists(strtolower($param), $this->parameters);
    }

    /**
     *
     * @return mixed
     */
    public function all()
    {
        return $this->getParameters();
    }

    /**
     * getRaw
     *
     *
     * @return mixed
     */
    public function getRaw()
    {
        return $this->all();
    }

    /**
     * merge
     *
     * @param ParameterInterface $parameters
     *
     * @return mixed
     */
    public function merge(ParameterInterface $parameters)
    {
        if (!$parameters instanceof StaticParameters) {
            throw new \InvalidArgumentException(
                sprintf('%s can only be merged with as static parameter collection', get_class($this))
            );
        }

        $this->parameters = array_merge($this->getParameters(), $parameters->all());
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($key, $value)
    {
        $this->set($key);
    }

    /**
     * getParameters
     *
     * @return mixed
     */
    private function &getParameters()
    {
        return $this->parameters;
    }
}
