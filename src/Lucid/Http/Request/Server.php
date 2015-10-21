<?php

/*
 * This File is part of the Lucid\Http\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Request;

use Lucid\Http\Parameters;

/**
 * @class Server
 * @see Parameters
 * @see ServerInterface
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Server extends Parameters implements ServerInterface
{
    /**
     * headers
     *
     * @var array
     */
    private $headers;

    /**
     * Constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        parent::__construct(array_merge(static::getDefaultServerVars(), $parameters));

        $this->initialize();
    }

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
                0 === strpos($lkey, 'x_http') ||
                0 === strpos($lkey, 'content_')
            ) {
                $this->headers[$key] = $value;
            }
        }
    }

    /**
     * initialize
     *
     * @return void
     */
    protected function initialize()
    {
        $uri = null;

        if ($this->isIIS() && null !==($uri = $this->fixIIsHeaders())) {
            return $this->parameters['REQUEST_URI'] = $uri;
        }

        if ($this->has('REQUEST_URI')) {
            $uri = preg_replace('~^[^/:]+://[^/]+~', '', $this->get('REQUEST_URI'));
        } elseif ($this->has('ORIG_PATH_INFO')) {
            // set URI from original path info and add the query string.
            $uri = $this->get('ORIG_PATH_INFO');

            if (0 < strlen($qs = $this->get('QUERY_STRING', ''))) {
                $uri .= '?'.$qs;
            }

            unset($this->parameters['ORIG_PATH_INFO']);
        }

        return $this->parameters['REQUEST_URI'] = $uri;
    }

    /**
     * fixIIsHeaders
     *
     * @return string|null
     */
    protected function fixIIsHeaders()
    {
        $uri = null;

        if (null !== ($uri = $this->get('HTTP_X_ORIGINAL_URL'))) {
            // IIS >= 7.0 with ISAPI_Rewrite
            unset($this->parameters['HTTP_X_ORIGINAL_URL']);
            unset($this->parameters['UNENCODED_URL']);
            unset($this->parameters['IIS_WasUrlRewritten']);
        } elseif (null !== ($uri = $this->get('HTTP_X_REWRITE_URL'))) {
            // Microsoft IIS with rewrite
            unset($this->parameters['HTTP_X_REWRITE_URL']);
        } elseif (1 === (int)$this->get('IIS_WasUrlRewritten', '0') &&
            0 < strlen($uenc = $this->get('UNENCODED_URL', ''))
        ) {
            $uri = $uenc;
        }

        return $uri;
    }

    /**
     * isIIS
     *
     * @return boolean
     */
    protected function isIIS()
    {
        if (null !== ($srv = $this->get('SERVER_SOFTWARE')) && false !== strpos(strtolower($srv), "microsoft-iis")) {
            return true;
        }

        return false;
    }

    /**
     * getDefaultServerVars
     *
     * @return array
     */
    private static function getDefaultServerVars(array $server = [])
    {
        $time = isset($server['REQEST_TIME_FLOAT']) ? $server['REQEST_TIME_FLOAT'] : microtime(true);

        return array_merge(
            [
                'SERVER_NAME'          => 'localhost',
                'SERVER_PORT'          => 80,
                'SERVER_PROTOCOL'      => 'http/1.1',
                'HTTP_HOST'            => 'localhost',
                'HTTP_USER_AGENT'      => 'lucid/1.0',
                'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'HTTP_ACCEPT_LANGUAGE' => 'en-US;q=0.6,en;q=0.4',
                'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
                'REMOTE_ADDR'          => '127.0.0.1',
                'SCRIPT_NAME'          => null,
                'SCRIPT_FILENAME'      => null,
                'REQEST_METHOD'        => 'GET',
                'REQEST_TIME_FLOAT'    => $time,
                'REQEST_TIME'          => (int)$time,
                'QUERY_STRING'         => null
            ],
            $server
        );
    }
}
