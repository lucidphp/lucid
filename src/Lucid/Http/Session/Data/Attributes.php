<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Session\Data;

use Lucid\Http\ParametersMutable;

/**
 * @class Attributes
 * @see AttributesInterface
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Attributes extends ParametersMutable implements AttributesInterface
{
    const DEFAULT_NAME = 'attributes';
    const DEFAULT_KEY  = '_attrs';
    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * storageKey
     *
     * @var string
     */
    protected $key;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $key
     * @param array $attributes
     */
    public function __construct($name = self::DEFAULT_NAME, $key = self::DEFAULT_KEY, array &$attributes = [])
    {
        $this->name = $name;
        $this->key  = $key;
        $this->initialize($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $data = [];
        $this->initialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array &$data)
    {
        $this->parameters = &$data;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $attributes)
    {
        $this->parameters = array_merge($this->parameters, $attributes);
    }
}
