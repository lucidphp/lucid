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

use Lucid\Http\Request;
use Psr\Http\Message\IncomingRequestInterface;

/**
 * @class RequestWrapper
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RequestWrapper extends Request
{
    /**
     * Constructor.
     *
     * @param IncomingRequestInterface $request
     */
    public function __construct(IncomingRequestInterface $request)
    {
        parent::__construct(
            $request->getQueryParams(),
            $request->getBodyParams(),
            $request->getAttributes(),
            $request->getFileParams(),
            $request->getCookieParams(),
            $request->getServerParams()
        );

        $this->origin = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->origin->getMethod();
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->origin->getProtocolVersion();
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->origin->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {
        return $this->origin->getQueryParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getBodyParams()
    {
        return $this->origin->getBodyParams();
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $this->origin->getServerParams();
    }

    /**
     * Get the original request object
     *
     *
     * @return IncomingRequestInterface
     */
    public function getOriginalRequest()
    {
        return $this->origin;
    }
}
