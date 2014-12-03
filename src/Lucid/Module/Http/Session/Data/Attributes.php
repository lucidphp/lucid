<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Data;

use Lucid\Module\Http\ParametersMutable;

/**
 * @class Attributes
 * @see AttributesInterface
 * @abstract
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class Attributes extends ParametersMutable implements AttributesInterface
{
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
     * @param mixed $name
     * @param mixed $key
     * @param array $attributes
     */
    public function __construct($name, $key, array &$attributes = [])
    {
        $this->key = $key;
        $this->name = $name;
        $this->initialize($attributes);
    }

    /**
     * getName
     *
     *
     * @return void
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
