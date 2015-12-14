<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache;

use Closure;

/**
 * @class Section
 * @see CacheInterface
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Section implements CacheInterface, SectionableInterface
{
    /** @var CacheInterface */
    private $cache;

    /** @var string */
    private $section;

    /**
     * Constructor.
     *
     * @param CacheInterface $storage
     * @param string $section
     */
    public function __construct(CacheInterface $storage, $section)
    {
        $this->cache   = $storage;
        $this->section = $section;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if ($value = $this->cache->get($this->getItemKey($key))) {
            return $value;
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data, $expires = null, $compressed = false)
    {
        return $this->cache->set($this->getItemKey($key), $data, $expires, $compressed);
    }

    /**
     * {@inheritdoc}
     */
    public function persist($key, $data, $compressed = false)
    {
        return $this->cache->persist($this->getItemKey($key), $data, $compressed);
    }

    /**
     * {@inheritdoc}
     */
    public function section($section)
    {
        return new static($this, $section);
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key, $value = 1)
    {
        return $this->cache->increment($this->getItemKey($key), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function decrement($key, $value = 1)
    {
        return $this->cache->decrement($this->getItemKey($key), $value);
    }

    /**
     * {@inheritdoc}
     */
    public function purge($key = null)
    {
        if (null === $key) {
            return $this->cache->increment($this->getKey());
        }

        return $this->cache->purge($this->getItemKey($key));
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return null !== $this->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function setUsing($key, callable $callback, $expires = null, $compressed = false)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $this->set($key, $data = $callback(), $expires, $compressed);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function persistUsing($key, callable $callback, $compressed = false)
    {
        return $this->persist($key, $callback(), $compressed);
    }

    /**
     * getKey
     *
     * @access protected
     * @return mixed
     */
    protected function getKey()
    {
        return sprintf('section:%s:key', $this->section);
    }

    /**
     * getItemKey
     *
     *
     * @return mixed
     */
    protected function getItemKey($key)
    {
        return sprintf('%s:%s:%s', $this->getSectionKey(), $this->section, $key);
    }

    /**
     * getSectionKey
     *
     * @return mixed
     */
    protected function getSectionKey()
    {
        if (null === ($key = $this->cache->get($skey = $this->getKey()))) {
            $this->cache->persist($skey, $key = rand(1, 10000));
        }

        return $key;
    }
}
