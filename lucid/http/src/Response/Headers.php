<?php

/*
 * This File is part of the Lucid\Http\Response package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Response;

use Lucid\Http\ParameterMutableInterface;
use Lucid\Http\Request\Headers as RequestHeaders;

/**
 * @class Headers
 *
 * @package Lucid\Http\Response
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Headers extends RequestHeaders implements ResponseHeaderInterface, ParameterMutableInterface
{
    private $cookies = [];

    /**
     * {@inheritdoc}
     */
    public function set($header, $value)
    {
        return $this->setHeader($this->parameters, $header, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function add($header, $value)
    {
        return $this->addHeader($this->parameters, $header, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($header)
    {
        unset($this->parameters[$header]);
    }

    /**
     * send
     *
     * @param boolean $replace
     * @param int $status
     *
     * @return void
     */
    public function sendAll($replace = false, $status = null)
    {
        foreach ($this->all() as $header => $values) {
            $this->doSendHeader($header, $values);
        }
    }

    /**
     * send
     *
     * @param mixed $name
     * @param mixed $replace
     * @param mixed $status
     *
     * @return void
     */
    public function send($name, $replace = false, $status = null)
    {
        if (!isset($this->headers[$name])) {
            return;
        }

        $this->doSendHeader($name, $this->headers[$name], $replace, $status);
    }

    /**
     * doSendHeader
     *
     * @param mixed $name
     * @param mixed $replace
     * @param mixed $status
     *
     * @return void
     */
    protected function doSendHeader($name, array $values, $replace, $status)
    {
        foreach ($values as $value) {
            header(sprintf('%s: %s', $name, $value), $replace, $status);
        }
    }

    /**
     * addCookie
     *
     * @param CookieInterface $cookie
     *
     * @return void
     */
    public function addCookie(CookieInterface $cookie)
    {
        $this->cookies[$cookie->getName()] = $cookie;
    }

    /**
     * addCookie
     *
     * @param CookieInterface $cookie
     *
     * @return void
     */
    public function removeCookie($name)
    {
        unset($this->cookies[$name]);
    }

    /**
     * getCookie
     *
     * @param mixed $name
     *
     * @return void
     */
    public function getCookie($name)
    {
        return isset($this->cookies[$name]) ? $this->cookies[$name] : null;
    }

    /**
     * getCookies
     *
     * @return void
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * setCookies
     *
     * @param array $cookies
     *
     * @return void
     */
    protected function setCookies(array $cookies)
    {
        $this->cookies = [];

        foreach ($cookies as $name => $cookie) {
            $this->addCookie($cookie);
        }
    }
}
