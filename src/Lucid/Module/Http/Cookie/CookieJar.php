<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Cookie;

/**
 * @class CookieJar
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CookieJar implements CookieJarInterface
{
    private $raw;
    private $names;
    private $cookies;

    /**
     * Constructor.
     *
     * @param array $cookies raw cookies as array.
     */
    public function __construct(array $cookies = [])
    {
        $this->raw = $cookies;
        $this->cookies = [];
    }

    /**
     * {@inheritdoc}
     */
    public function set(CookieInterface $cookie)
    {
        $domain = $this->getDomain($cookie->getDomain());
        $path = $cookie->getPath();

        $this->cookies[$domain][$path][$cookie->getName()] = $cookie;
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, $path = '/', $domain = null)
    {
        return isset($this->cookies[$this->getDomain($domain)][$path ?: '/']);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $path = '/', $domain = null)
    {
        if (isset($this->cookies[$domain = $this->getDomain($domain)][$path = $path ?: '/'][$name])) {
            return $this->cookies[$domain][$path][$name];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeByDomain($domain = self::DOMAIN_DEFAULT)
    {
        unset($this->cookies[$this->getDomain($domain)]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name, $path = '/', $domain = null)
    {
        if (!isset($this->cookies[$domain = $this->getDomain($domain)][$path = $path ?: '/'][$name])) {
            return false;
        }

        unset($this->cookies[$domain][$path][$name]);

        if (empty($this->cookies[$domain][$path])) {
            unset($this->cookies[$domain][$path]);
        }

        if (empty($this->cookies[$domain])) {
            unset($this->cookies[$domain]);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setCleared(CookieInterface $c)
    {
        $this->clear($c->getName(), $c->getPath(), $c->getDomain(), $c->isSecure(), $c->isHttpOnly());
    }

    /**
     * {@inheritdoc}
     */
    public function clear($name, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        $this->set(new Cookie($name, null, 1, $path, $domain, $secure, $httpOnly));
    }

    /**
     * {@inheritdoc}
     */
    public function all($type = self::OUTPUT_FLAT)
    {
        if (self::OUTPUT_NESTED === $type) {
            return $this->cookies;
        }

        $out = [];

        foreach ($this->cookies as $domain => $paths) {
            foreach ($paths as $path => $cookies) {
                foreach ($cookies as $cookie) {
                    $out[] = $cookie;
                }
            }
        }

        return $out;
    }

    /**
     * getDomain
     *
     * @param mixed $domain
     *
     * @return void
     */
    protected function getDomain($domain = null)
    {
        return $domain ?: self::DOMAIN_DEFAULT;
    }
}
