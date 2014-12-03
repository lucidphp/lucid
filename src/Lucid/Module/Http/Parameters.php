<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http;

/**
 * @class Parameters
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Parameters implements ParameterInterface
{
    protected $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return isset($this->parameters[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        return array_keys($this->parameters);
    }
}
