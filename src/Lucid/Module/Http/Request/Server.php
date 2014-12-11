<?php

/*
 * This File is part of the Lucid\Module\Http\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Request;

use Lucid\Module\Http\Parameters;

/**
 * @class Server
 * @see HeaderAwareParameterInterface
 * @see Parameters
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Server extends Parameters implements HeaderAwareParameterInterface
{
    /**
     * headers
     *
     * @var array
     */
    private $headers;

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        if (null === $this->headers) {
            $this->setHeaders();
        }

        return $this->headers;
    }

    /**
     * Extract http headers from the server array.
     *
     * @return void
     */
    protected function setHeaders()
    {
        $this->headers = [];

        foreach ($this->parameters as $key => $value) {
            if (0 === strpos($lkey = strtolower($key), 'http_') ||
                0 === strpos($lkey, 'x_http_') ||
                0 === strpos($lkey, 'content_')
            ) {
                $this->headers[$key] = $value;
            }
        }
    }
}
