<?php

/*
 * This File is part of the Lucid\Http\Cookie package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Cookie;

use DateTime;
use InvalidArgumentException;

/**
 * @class Cookie
 *
 * @package Lucid\Http\Cookie
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Cookie implements CookieInterface
{
    /**
     * Constructor.
     *
     * @param string $name
     * @param string $value
     * @param string $domain
     * @param string $path
     * @param mixed $expires
     * @param boolean $secure
     * @param boolean $httpOnly
     */
    public function __construct(
        $name,
        $value = null,
        $expires = 0,
        $path = null,
        $domain = null,
        $secure = false,
        $httpOnly = false
    ) {
        $this->setName($name);
        $this->setExpireTime($expires);

        $this->value = $value;
        $this->domain = $domain;
        $this->path = $path ?: '/';
        $this->secure = (bool)$secure;
        $this->httpOnly = (bool)$httpOnly;
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpireTime()
    {
        return $this->expire;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * {@inheritdoc}
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        return $this->expire < time();
    }

    public function isDeleted()
    {
        return null === ($val = $this->getValue()) || 0 === strlen((string)$val);
    }

    /**
     * {@inheritdoc}
     */
    public function setDeleted()
    {
        $this->value = null;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpired()
    {
        $this->expire = time() - 31536000;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $cookie = ['name=' . urlencode($this->getName())];

        if (0 === strlen($value = (string)$this->getValue())) {
            $time = time() - 31536000; // invalidate by one year.
            $cookie[] = 'deleted';
        } else {
            $time = $this->getExpireTime();
            $cookie[] = 'value=' . urlencode($value);
        }

        $cookie[] = 'expires=' . gmdate(DateTime::COOKIE, $time);

        if (null !== $this->getPath()) {
            $cookie[] = 'path=' . $this->getPath();
        }

        if (null !== $this->getDomain()) {
            $cookie[] = 'domain=' . $this->getDomain();
        }

        if ($this->isSecure()) {
            $cookie[] = 'secure';
        }

        if ($this->isHttpOnly()) {
            $cookie[] = 'httponly';
        }

        return sprintf('Set-Cookie: %s;', trim(implode('; ', $cookie)));
    }

    /**
     * setName
     *
     * @param mixed $name
     *
     * @return void
     */
    protected function setName($name)
    {
        // Check for invalid characters in name
        // @see http://curl.haxx.se/rfc/cookie_spec.html
        if (preg_match('/[=,; \t\n\r]/', $name)) {
            throw new InvalidArgumentException();
        }

        $this->name = $name;
    }

    /**
     * setExpireTime
     *
     * @param mixed $time
     *
     * @return void
     */
    protected function setExpireTime($time)
    {
        if (is_int($time)) {
            $time = 0 >= $time ? 0 : $time;
        } elseif (is_string($time) && false === ($time = @strtotime($time))) {
            throw new InvalidArgumentException('Invalid time format.');
        } elseif ($time instanceof DateTime) {
            $time = (int)$time->format('U');
        }

        $this->expire = $time;
    }
}
