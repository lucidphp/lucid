<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http;

/**
 * @class Parameters
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ParametersMutable extends Parameters implements ParameterMutableInterface
{
    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        unset($this->parameters[$key]);
    }
}
