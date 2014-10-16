<?php

/*
 * This File is part of the Lucid\Module\Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Http;

use Symfony\Component\HttpFoundation\Request;

/**
 * @class RequestContext
 *
 * @package Lucid\Module\Routing\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RequestContext implements RequestContextInterface
{
    /**
     * Constructor.
     *
     * @param string $path
     * @param string $method
     * @param string $query
     * @param string $host
     * @param string $scheme
     */
    public function __construct($path, $method = 'GET', $query = '', $host = 'localhost', $scheme = 'http')
    {
        $this->uri = $path;
        $this->method = $method;
        $this->query = $query ?: '';
        $this->host = $host;
        $this->scheme = $scheme;
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
     * Creates a RequestContext object from a Symfony Request object.
     *
     * @param Request $request
     *
     * @return RequestContext
     */
    public static function fromRequest(Request $request)
    {
        return new self(
            $request->getPathInfo(),
            $request->getMethod(),
            $request->getQueryString(),
            $request->getHost(),
            $request->getScheme()
        );
    }
}
