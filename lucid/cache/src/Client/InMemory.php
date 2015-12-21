<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Cache\Client;

use ArrayObject;

/**
 * @class ArrayClient
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class InMemory extends AbstractClient
{
    /** @var ArrayObject */
    private $storage;

    /** @var bool */
    private $persist;

    /** @var string */
    private $persistPath;

    /**
     * Constructor.
     *
     * @param bool $persist
     * @param string $path
     */
    public function __construct($persist = false, $path = '')
    {
        $this->persistPath = $path;
        $this->persist     = (bool)$persist;
        $this->boot();
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        //$this->persistStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        return isset($this->storage[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function read($key)
    {
        if ($this->exists($key)) {
            return $this->storage[$key];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $data, $expires = 60, $compressed = false)
    {
        $this->storage[$key] = $data;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function saveForever($key, $data, $compressed = false)
    {
        return $this->write($key, $data, 0, $compressed);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        unset($this->storage[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        unset($this->storage);

        $this->storage = new \ArrayObject;
        $this->persistStorage();
    }

    /**
     * {@inheritdoc}
     */
    protected function incrementValue($key, $value)
    {
        return $this->storage[$key] = (int)$this->storage[$key] + (int)$value;
    }

    /**
     * {@inheritdoc}
     */
    protected function decrementValue($key, $value)
    {
        return $this->storage[$key] = (int)$this->storage[$key] - (int)$value;
    }

    /**
     * setUpStorage
     *
     * @return void
     */
    private function boot()
    {
        if ($this->persist and file_exists($this->persistPath)) {
            try {
                $this->storage = unserialize(file_get_contents($this->persistPath));
            } catch (\Exception $e) {
                $this->storage = new ArrayObject;
            }
            return;
        }

        $this->storage = new ArrayObject;
    }

    /**
     * persistStorage
     *
     *
     * @return mixed
     */
    private function persistStorage()
    {
        if ($this->persist) {
            file_put_contents($this->persistPath, serialize($this->storage), LOCK_EX);
        }
    }
}
