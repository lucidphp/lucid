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

use InvalidArgumentException;
use Lucid\Cache\CacheInterface;
use Lucid\Cache\ClientInterface;

/**
 * @class AbstractClient
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractClient implements ClientInterface
{
    /** @var float */
    protected $defaultExpiry = 60;

    /**
     * {@inheritdoc}
     */
    public function saveForever($key, $data, $compressed = false)
    {
        return $this->write($key, $data, $this->getPersistLevel(), $compressed);
    }

    /**
     * increment
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function increment($key, $value)
    {
        $this->validateIncrementValue($value);

        return $this->incrementValue($key, $value);
    }

    /**
     * decrement
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function decrement($key, $value)
    {
        $this->validateIncrementValue($value);

        return $this->decrementValue($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function parseExpireTime($expires)
    {
        return $this->expiryToSeconds($expires);
    }

    /**
     * get default expiry time
     */
    public function getDefaultExpiry()
    {
        return $this->$defaultExpiry;
    }

    /**
     * validateIncrementValue
     *
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     * @access private
     * @return void
     */
    private function validateIncrementValue($value)
    {
        if (!is_int($value) || $value < 1) {
            throw new InvalidArgumentException('Value must be Integer and greater that zero');
        }
    }

    /**
     * incrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access public
     * @abstract
     * @return mixed
     */
    abstract protected function incrementValue($key, $value);

    /**
     * decrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access protected
     * @abstract
     * @return mixed
     */
    abstract protected function decrementValue($key, $value);

    /**
     * expiryToSeconds
     *
     * @param int|string $expiry
     *
     * @return int
     */
    protected function expiryToSeconds($expiry)
    {
        if (is_string($expiry = $this->validateExpires($expiry))) {
            return strtotime($expiry) - time();
        }

        return CacheInterface::PERSIST === $expiry ? $this->getPersistLevel() : $expiry * 60;
    }

    /**
     * expiryToUnixTimestamp
     *
     * @param mixed $expiry
     *
     * @return int
     */
    protected function expiryToUnixTimestamp($expiry)
    {
        if (is_string($expiry = $this->validateExpires($expiry))) {
            return strtotime($expiry);
        }

        return CacheInterface::PERSIST === $expiry ? $this->getPersistLevel() : time()  + ($expiry * 60);
    }

    /**
     * validateExpires
     *
     * @param mixed $expiry
     *
     * @return int
     */
    protected function validateExpires($expires)
    {
        if (($str = is_string($expires)) && false === @strtotime($expires)) {
            throw new InvalidArgumentException(sprintf('Invalid expiry time "%s".', $expires));
        }

        return $str ? $expires : (int)$expires;
    }

    /**
     * getPersistLevel
     *
     * @return int
     */
    protected function getPersistLevel()
    {
        return 0;
    }
}
