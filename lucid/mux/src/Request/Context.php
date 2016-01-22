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

use Psr\Http\Message\ServerRequestInterface;

/**
 * @class Context
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Context implements ContextInterface
{
    /** @var string */
    private $path;

    /** @var string */
    private $method;

    /** @var string */
    private $host;

    /** @var string */
    private $scheme;

    /** @var string */
    private $query;

    /** @var int */
    private $port;

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
        $path = '/',
        $method = 'GET',
        $query = '',
        $host = 'localhost',
        $scheme = 'http',
        $port = 80
    ) {
        $this->path       = $path;
        $this->method     = $method;
        $this->query      = ltrim($query ?: '', '?&');
        $this->host       = trim($host, '/');
        $this->scheme     = $scheme;
        $this->port       = (int)$port;
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

    /**
     * fromPsrRequest
     *
     * @param ServerRequestInterface $request
     *
     * @return ServerRequestInterface
     */
    public static function fromPsrRequest(ServerRequestInterface $request)
    {
        $uri    = $request->getUri();
        $server = $request->getServerParams();

        return new self(
            $uri->getPath(),
            $request->getMethod(),
            $uri->getQuery(),
            $uri->getHost(),
            $uri->getScheme(),
            $uri->getPort() ?: (isset($server['SERVER_PORT']) ? $server['SERVER_PORT'] : 80)
        );
    }
}
