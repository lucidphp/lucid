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

/**
 * @class Configuration
 *
 * @package Lucid\Config
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Configuration
{
    /**
     * parameters
     *
     * @var ParameterInterface
     */
    private $parameters;

    /**
     * Constructor.
     *
     * @param ParameterInterface $params
     */
    public function __construct(ParameterInterface $params = null)
    {
        $this->parameters = $params ?: new Parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if ($this->parameters->has($key)) {
            return $this->parameters->get($key);
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->parameters->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function optimize()
    {
        if ($this->parameters instanceof StaticParameters) {
            return;
        }

        if (!$this->parameters instanceof ResolvableInterface) {
            $this->parameters->resolve();
        }

        $this->parameters = new StaticParameters($this->parameters->all());
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked()
    {
        return $this->parameters instanceof StaticParameters;
    }
}
