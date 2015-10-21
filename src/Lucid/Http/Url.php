<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http;

use Psr\Http\Message\UriInterface;

/**
 * @class Uri
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Url implements UriInterface
{
    /**
     * path
     *
     * @var string
     */
    private $path;

    /**
     * query
     *
     * @var string
     */
    private $query;

    /**
     * scheme
     *
     * @var string
     */
    private $scheme;

    /**
     * host
     *
     * @var string
     */
    private $host;

    /**
     * port
     *
     * @var int
     */
    private $port;

    /**
     * user
     *
     * @var string
     */
    private $user;

    /**
     * pass
     *
     * @var string
     */
    private $pass;

    /**
     * Constructor.
     *
     * @param string $path
     * @param string $scheme
     * @param string $host
     * @param int $port
     * @param string $query
     * @param string $frag
     * @param string $user
     * @param string $pass
     */
    public function __construct(
        $path,
        $scheme = 'http',
        $host = 'localhost',
        $port = 80,
        $query = '',
        $frag = '',
        $user = null,
        $pass = null
    ) {
        $this->path = $path;
        $this->withScheme($scheme);
        $this->withHost($host);
        $this->withPort((int)$port);
        $this->withQuery($query ?: '');
        $this->withFragment($frag ?: '');
        $this->user = $user ?: null;
        $this->pass = $pass ?: null;
    }

    /**
     * {@inheritdoc
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc
     */
    public function getAuthority()
    {
        if ('' === $userInfo = $this->getUserInfo()) {
            return '';
        }

        $authority = sprintf('%s@%s', $userInfo, $this->host);

        if (false === $this->isDefaultPort()) {
            $authority .= ':'.(string)$this->port;
        }

        return $authority;
    }

    /**
     * {@inheritdoc
     */
    public function getUserInfo()
    {
        if (null === $this->user) {
            return '';
        }

        if (null !== $this->pass) {
            return sprintf('%s:%s', $this->user, $this->pass);
        }

        return $this->user;
    }

    /**
     * {@inheritdoc
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc
     */
    public function getPort()
    {
        return $this->port;
    }



    /**
     * {@inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc
     */
    public function getQuery()
    {
        return $this->query;
    }



    /**
     * {@inheritdoc
     */
    public function getFragment()
    {
        return $this->fragment;
    }



    /**
     * {@inheritdoc
     */
    public function withScheme($scheme)
    {
        $this->scheme = strtolower((string)$scheme);
    }


    /**
     * {@inheritdoc
     */
    public function withUserInfo($user, $password = null)
    {
        $this->user = $user;
        $this->pass = $password;
    }


    /**
     * {@inheritdoc
     */
    public function withHost($host)
    {
        $this->host = (string)$host;
    }

    /**
     * {@inheritdoc
     */
    public function withPort($port)
    {
        $this->port = (int)$port;
    }

    /**
     * {@inheritdoc
     */
    public function withPath($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc
     */
    public function withQuery($query)
    {
        $this->query = ltrim($query, '?&');
    }

    /**
     * {@inheritdoc
     */
    public function withFragment($fragment)
    {
        $this->fragment = ltrim($fragment, '#');
    }

    /**
     * {@inheritdoc
     */
    public function __toString()
    {
        return $this->composeUrl();
    }

    /**
     * fromString
     *
     * @param string $url
     *
     * @return UriInterface
     */
    public static function fromString($url)
    {
        extract(parse_url((string)$url));

        return new self($path, $scheme, $host, (int)$port, $query, $fragment, $user, $pass);
    }

    /**
     * composeUrl
     *
     * @return string
     */
    private function composeUrl()
    {
        $authority = $this->getAuthority();
        $port = '' === $authority ? '' : ($this->isDefaultPort() ? '' : ':'.$this->port);
        $query = '' === $this->query ? '' : '?' . $this->query;
        $fragment = '' === $this->fragment ? '' : '#' . $this->fragment;

        return sprintf('%s://%s%s/%s', $this->scheme, $authority, $port, $this->path, $query, $fragment);
    }

    /**
     * isDefaultPort
     *
     * @return bool
     */
    private function isDefaultPort()
    {
        if (80 === $this->port && 'http' === $this->scheme) {
            return true;
        }

        if (443 === $this->port && 'https' === $this->scheme) {
            return true;
        }

        return false;
    }
}
