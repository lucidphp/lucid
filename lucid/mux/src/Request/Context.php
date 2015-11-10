<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Request;

use Psr\Http\Message\RequestInterface;

/**
 * @class Context
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Context implements ContextInterface
{
    /**
     * base
     *
     * @var string
     */
    private $base;

    /**
     * path
     *
     * @var string
     */
    private $path;

    /**
     * method
     *
     * @var string
     */
    private $method;

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
     * scheme
     *
     * @var string
     */
    private $scheme;

    /**
     * query
     *
     * @var string
     */
    private $query;

    /**
     * Constructor.
     *
     * @param string $base
     * @param string $path
     * @param string $method
     * @param string $query
     * @param string $host
     * @param string $scheme
     * @param int $port
     */
    public function __construct(
        $base = '',
        $path = '/',
        $method = 'GET',
        $query = '',
        $host = 'localhost',
        $scheme = 'http',
        $port = 80
    ) {
        $this->base       = $base;
        $this->uri        = $path;
        $this->method     = $method;
        $this->query      = ltrim($query ?: '', '?&');
        $this->host       = trim($host, '/');
        $this->scheme     = $scheme;
        $this->port       = (int)$port;
    }

    /**
     * getBaseUrl
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->base;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryString()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * getHttpPort
     *
     * @return int
     */
    public function getHttpPort()
    {
        return $this->port;
    }
}
