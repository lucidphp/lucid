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
class Uri implements UriInterface
{
    /**
     * Unreserved characters as specified by RFC 3986, section 2.3
     *
     * @see http://tools.ietf.org/html/rfc3986#section-2.3
     *
     * @var string
     */
    const UR_CHARS = 'A-Za-z0-9\-\._\~';

    /**
     * Query and fragment sub-delimiter as specified by RFC 3986.
     *
     * @see http://tools.ietf.org/html/rfc3986
     *
     * @var string
     */
    const SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * Valid url schemes.
     *
     * @var string
     */
    private $validSchemes = ['http' => 80, 'https' => 443];

    /**
     * Default Url part values.
     *
     * @var array
     */
    private static $defaults = [
        'path'  => '/',  'scheme'   => 'http', 'host' => 'localhost', 'port' => 80,
        'query' => null, 'fragment' => null,   'user' => null,        'pass' => null
    ];

    /**
     * Path of the uri.
     *
     * @var string
     */
    private $path;

    /**
     * Query string of the Uri.
     *
     * @var string
     */
    private $query;

    /**
     * Scheme as string.
     *
     * @var string
     */
    private $scheme;

    /**
     * Host as string.
     *
     * @var string
     */
    private $host;

    /**
     * Port of the Url.
     *
     * @var int
     */
    private $port;

    /**
     * Username.
     *
     * @var string
     */
    private $user;

    /**
     * User password.
     *
     * @var string
     */
    private $pass;

    /**
     * Compiled url.
     *
     * @var string
     */
    private $compiled;

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
        $scheme = null,
        $host = null,
        $port = null,
        $query = null,
        $fragment = null,
        $user = null,
        $password = null
    ) {
        $this->initialize($path, $scheme, $host, $port, $query, $fragment, $user, $password);
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
            return $userInfo;
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
        if (!$this->isValidScheme()) {
            throw new \InvalidArgumentException;
        }

        $uri = clone $this;

        if ($scheme !== $this->scheme) {
            $uri->scheme = $scheme;
        }

        return $uri;
    }


    /**
     * {@inheritdoc
     */
    public function withUserInfo($user, $password = null)
    {
        $uri = clone $this;

        if ($uri->user === $user && $uri->password === $password) {
            return $uri;
        }

        $uri->user = $user;
        $uri->password = $password;
    }


    /**
     * {@inheritdoc
     */
    public function withHost($host)
    {
        $uri = clone $this;

        if ($this->host === $host) {
            return $uri;
        }

        $uri->host = (string)$host;

        return $uri;
    }

    /**
     * {@inheritdoc
     */
    public function withPort($port)
    {
        $uri = clone $this;

        if ($this->port === $port) {
            return $uri;
        }

        $uri->port = (int)$port;

        return $uri;
    }

    /**
     * {@inheritdoc
     */
    public function withPath($path)
    {
        $uri = clone $this;

        if ($this->path === $path) {
            return $uri;
        }

        $uri->path = $path;

        return $uri;
    }

    /**
     * {@inheritdoc
     */
    public function withQuery($query)
    {
        $uri = clone $this;
        $uri->query = $this->sanitizeQuery($query);

        return $uri;
    }

    /**
     * {@inheritdoc
     */
    public function withFragment($fragment)
    {
        $uri = clone $this;
        $uri->fragment = ltrim($fragment, '#');

        return $uri;
    }

    /**
     * {@inheritdoc
     */
    public function __toString()
    {
        if (null === $this->compiled) {
            $this->compiled = $this->composeUrl();
        }

        return $this->compiled;
    }

    public function __clone()
    {
        $this->compiled = null;
    }

    /**
     * fromString
     *
     * @param string $url
     *
     * @return UriInterface
     */
    //public static function fromString($url)
    //{
        //extract(array_merge(static::$defaults, static::parseUrl($url)));

        //return new self($path, $scheme, $host, $port, $query, $fragment, $user, $pass);
    //}

    /**
     * setScheme
     *
     * @param string $scheme
     *
     * @return void
     */
    private function setScheme($scheme = 'http')
    {
        if (!$this->isValidScheme($scheme)) {
            throw new \InvalidArgumentException(sprintf('Given scheme %s is invalid.', (string)$scheme));
        }

        $this->scheme = strtolower((string)$scheme);
    }

    /**
     * setHost
     *
     * @param string $host
     *
     * @return void
     */
    private function setHost($host)
    {
        $this->host = (string)$host;
    }

    /**
     * setPort
     *
     * @param int $port
     *
     * @return void
     */
    private function setPort($port)
    {
        if (!is_int($port)) {
            throw new \InvalidArgumentException(sprintf('Port must be an integer, %s given', gettype($port)));
        }

        $this->port = $port;
    }

    /**
     * sanitizeQuery
     *
     * @param string $query
     *
     * @return string
     */
    private function sanitizeQuery($query)
    {
        return trim($query, '?&');
    }

    /**
     * setPath
     *
     * @param string $path
     *
     * @return void
     */
    private function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * setFragment
     *
     * @param string $fragment
     *
     * @return void
     */
    private function setFragment($fragment)
    {
        if (0 === strpos('#', $fragment)) {
            $fragment = substr($fragment, 1);
        }

        $this->fragment = $fragment;
    }

    /**
     * Initializes the Uri oject with sensible data.
     *
     * @param string $path
     * @param string $scheme
     * @param int $host
     * @param int $port
     * @param string $query
     * @param string $fragment
     * @param string $user
     * @param string $pass
     *
     * @return void
     */
    private function initialize($path, ...$args)
    {
        if (!preg_match("#^(https?|ftp|file)?:\/\/.*#", $path)) {
            $path = '//'.$path;
        }

        var_dump($path);

        $parts = static::parseUrl($path);

        array_unshift($args, $parts['path']);

        $args = array_combine($keys = array_keys(static::$defaults), $args);

        array_walk($args, function (&$val, $key) use (&$args, $parts) {
            $val = null !== $val ? $val : (isset($parts[$key]) ? $parts[$key] : static::$defaults[$key]);
        });

        array_map(function ($key) use ($args) {
            call_user_func([$this, 'set'.ucfirst($key)], $args[$key]);
        }, $keys);
    }

    /**
     * isValidScheme
     *
     * @param string $scheme
     *
     * @return bool
     */
    private function isValidScheme($scheme)
    {
        return array_key_exists(strtolower($scheme), $this->validSchemes);
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
        return isset($this->validSchemes[$this->scheme]) && $this->port === $this->validSchemes[$this->scheme];
    }

    private function filterUrlPath($string)
    {
        return $this->filterUrlString($string, $this->getPathRegexp());
    }

    private function filterQueryAndFragment($string)
    {
        return $this->filterUrlString($string, $this->getQueryReqexp());
    }

    private function setQuery($query)
    {
        if (null === $query || empty($query)) {
            $q = [];
        }

        parse_str($this->sanitizeQuery($query), $q);

        $this->query = $q;
    }

    private function setUser($user)
    {
        $this->user = $user;
    }

    private function setPass($password)
    {
        $this->password = $password;
    }

    /**
     * filterUrlString
     *
     * @param mixed $string
     * @param mixed $regexp
     *
     * @return string
     */
    //private function filterUrlString($string, $regexp)
    //{
    //    return preg_replace_callback($regexp, function ($matches) {
    //        return rawurldecode($matches[0]);
    //    }, $string);

    //}

    private function getPathRegexp()
    {
        $urChars = self::UR_CHARS;
        $subDelims = self::SUB_DELIMS;
        return <<<REGEX
{
(?:[^

    # Matches unreserved characters
    $urChars
    # Matches subdelimiter.
    $subDelims
    # Authority parts
    %:@\/\?
    ]
    # dont match already encoded parts
    +|%(?![A-Fa-f0-9]{2})
)
}
REGEX;
    }

    /**
     * parseUrl
     *
     * @param string $url
     *
     * @throws \InvalidArgumentException if $url is malformed.
     *
     * @return array
     */
    private static function parseUrl($url)
    {
        if (false === ($parts = parse_url($url)) || !isset($parts['path'])) {
            throw new \InvalidArgumentException('Given url seems to be malformed.');
        }

        return $parts;
    }
}
