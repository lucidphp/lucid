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

/**
 * @class MetaData
 * @see Attributes
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MetaData extends Attributes
{
    const CREATED = '__CREATED__';
    const UPDATED = '__UPDATED__';
    const TTL = '__TTL__';
    const DEFAULT_KEY = '_meta_';

    /**
     * lastUsed
     *
     * @var int
     */
    private $lastUsed;

    /**
     * updateThreshold
     *
     * @var int
     */
    private $updateThreshold;

    /**
     * Constructor.
     *
     * @param string $key
     * @param int $updateThreshold in minutes
     */
    public function __construct($key = self::DEFAULT_KEY, $updateThreshold = 120)
    {
        $this->setUpdateThreshold($updateThreshold);
        parent::__construct('meta_data', $key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        if ($this->validKey($key)) {
            parent::set($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if ($this->validKey($key)) {
            return parent::get($key, $default);
        }

        return $default;

    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        if ($this->validKey($key)) {
            parent::delete($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array &$data)
    {
        $this->parameters = &$data;

        $time = time();

        if (null === $this->get(self::CREATED, null)) {
            $this->markAsNew($time + $this->updateThreshold);

            return;
        }

        if ($time - ($this->lastUsed = $this->get(self::UPDATED, 0)) >= $this->updateThreshold) {
            $this->set(self::UPDATED, $time);
        }

        $this->setTtl($this->get(self::TTL, null));
    }

    /**
     * markNew
     *
     * @param mixed $ttl
     *
     * @return void
     */
    public function markAsNew($ttl = null)
    {
        $this->resetTimestamps(time());
        $this->setTtl($ttl);
    }

    /**
     * lastUsed
     *
     * @return int
     */
    public function getLastUsedTimestamp()
    {
        return $this->lastUsed ?: 0;
    }

    /**
     * created
     *
     * @return int|null
     */
    public function getCreationTimestamp()
    {
        return $this->get(self::CREATED, 0);
    }

    public function getTtl()
    {
        return $this->get(self::TTL, time());
    }

    /**
     * resetTimestamps
     *
     * @param mixed $time
     *
     * @return void
     */
    private function resetTimestamps($time = null)
    {
        $this->set(self::CREATED, $time);
        $this->set(self::UPDATED, $time);
        $this->lastUsed =  $time;
    }

    /**
     * setTtl
     *
     * @param mixed $ttl
     *
     * @return void
     */
    private function setTtl($ttl = null)
    {
        $this->set(self::TTL, null !== $ttl ? $ttl : (int)ini_get('session.cookie_lifetime'));
    }

    private function setUpdateThreshold($time)
    {
        if (is_numeric($time)) {
            $time = (int)$time * 60;
        } elseif (false !== ($now = @strtotime((string)$time))) {
            $time = $now - time();
        }

        $this->updateThreshold = $time;
    }

    /**
     * hasKey
     *
     * @param mixed $key
     *
     * @return boolean
     */
    private function validKey($key)
    {
        return in_array($key, [self::CREATED, self::UPDATED, self::TTL]);
    }
}
