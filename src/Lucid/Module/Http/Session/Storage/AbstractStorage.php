<?php

/*
 * This File is part of the Lucid\Module\Http\Session\Storage package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Storage;

use Lucid\Module\Http\Session\Data\AttributesInterface;

/**
 * @class AbstractStorage
 *
 * @package Lucid\Module\Http\Session\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractStorage implements StorageInterface
{
    /**
     * closed
     *
     * @var boolean
     */
    protected $closed;

    /**
     * started
     *
     * @var boolean
     */
    protected $started;

    /**
     * metaData
     *
     * @var Lucid\Module\Http\Session\Data\MetaData
     */
    protected $metaData;

    /**
     * attributes
     *
     * @var array
     */
    protected $attributes;

    /**
     * {@inheritdoc}
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attrs)
    {
        $this->attributes = [];

        foreach ($attrs as $attributes) {
            $this->addAttributes($attributes);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addAttributes(AttributesInterface $attrs)
    {
        $this->attributes[$attrs->getKey()] = $attrs;
    }

    /**
     * initializeSession
     *
     * @param array $data
     *
     * @return void
     */
    protected function initializeSession(array &$data = null)
    {
        foreach ($this->attributesAll() as $attrs) {
            $this->initializeAttributes($attrs, $data);
        }

        $this->closed = false;

        return $this->started = !$this->closed;
    }

    /**
     * initializeAttributes
     *
     * @param AttributesInterface $attrs
     * @param array $data
     *
     * @return void
     */
    protected function initializeAttributes(AttributesInterface $attrs, array &$data = [])
    {
        if (!isset($data[$key = $attrs->getKey()])) {
            $data[$key] = [];
        }

        $attrs->initialize($data[$key]);
    }

    /**
     * Merge all attributes
     *
     * @return array
     */
    protected function mergeAttributes()
    {
        $attrs = [];

        foreach ($this->attributesAll() as $attributes) {
            $attrs[$attributes->getKey()] = $attributes->all();
        }

        return $attrs;
    }

    /**
     * attributesAll
     *
     * @return array
     */
    protected function attributesAll()
    {
        return array_merge($this->attributes, [$this->metaData->getKey() => $this->metaData]);
    }
}
