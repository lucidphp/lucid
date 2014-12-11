<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http;

use Lucid\Module\Http\Parameters;
use Lucid\Module\Http\Request\Body;
use Lucid\Module\Http\Request\Files;
use Lucid\Module\Http\Request\Server;
use Lucid\Module\Http\Request\Headers;
use Lucid\Module\Http\Request\HeaderAwareParameterInterface;

/**
 * @class Request
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Request implements RequestInterface
{
    protected $version;
    protected $pathInfo;
    protected $host;
    protected $port;
    protected $method;
    protected $body;
    protected $request;
    protected $query;
    protected $content;
    protected $queryString;
    protected $requestUrl;
    protected $requestUri;

    /**
     * headers
     *
     * @var mixed
     */
    public $server;
    public $files;
    public $cookies;
    public $session;
    public $headers;
    public $attributes;

    /**
     * Constructor.
     *
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $files
     * @param array $cookies
     * @param array $server
     */
    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $files = [],
        array $cookies = [],
        array $server = [],
        array $content = null
    ) {
        $this->content = $content;

        $this->cookies = new Parameters($cookies);
        $this->files = new Files($files);

        $this->setServer($server);
        $this->setQuery($query);
        $this->setRequest($request);
        $this->setAttributes($attributes);
        $this->setHeaders($this->server);
    }

    /**
     * create
     *
     * @param string $path
     * @param string $method
     * @param array $cookies
     * @param array $files
     * @param array $server
     *
     * @return RequestInterface
     */
    public static function create(
        $path = '/',
        $method = 'GET',
        array $cookies = [],
        array $files = [],
        array $server = []
    ) {
    }

    /**
     * getProtocolVersion
     *
     * @return void
     */
    public function getProtocolVersion()
    {
        if (null === $this->version) {
            $this->version = $this->findHttpVersion();
        }

        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        if (null === $this->body()) {
            if (null === $this->content) {
                $body = Body::createFromInput(
                    0 === strcmp('multipart/form-data', $this->server->get('CONTENT_TYPE'))
                );
            } else {
                $body = Body::createFromString($this->content);
            }
        }

        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers->all();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($header)
    {
        return $this->headers->has($header);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($header)
    {
        if (null !== $res = $this->headers->get($header)) {
            return implode(', ', $res);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderAsArray($header)
    {
        $headers = [];

        foreach ($this->headers->all() as $header => $value) {
            $headers[$key] = implode(', ', $value);
        }

        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        if (null === $this->method) {
            $this->method = strtoupper($this->findRequestMethod());
        }

        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestUri()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getPort()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $this->server->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams()
    {
        return $this->cookies->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getFileParams()
    {
        return $this->files->getFilesArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {
        return $this->query->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getBodyParams()
    {
        return $this->request->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($attribute, $default = null)
    {
        return $this->attributes->get($attribute, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = new ParametersMutable($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($attribute, $value)
    {
        $this->attributes->set($attribute, $value);
    }

    /**
     * getQuery
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return void
     */
    public function getQuery($key, $default = null)
    {
        return $this->query->get($key, $default);
    }

    /**
     * getRequest
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return void
     */
    public function getRequest($key, $default = null)
    {
        return $this->request->get($key, $default);
    }

    /**
     * Creates a new request from super php globals
     *
     * @return RequestInterface
     */
    public static function createFromGlobals()
    {
        return new static($_GET, $_POST, [], $_FILES, $_COOKIE, $_SERVER);
    }

    /**
     * setServer
     *
     * @param array $server
     *
     * @return void
     */
    protected function setServer(array $server = [])
    {
        $this->server = new Server(static::getDefaultServerVars($server));
    }

    /**
     * setHeaders
     *
     * @param Server $server
     *
     * @return void
     */
    protected function setHeaders(HeaderAwareParameterInterface $server)
    {
        $this->headers = new Headers($server->getHeaders());
    }

    /**
     * setRequest
     *
     * @param array $post
     *
     * @return void
     */
    protected function setRequest(array $post)
    {
        $this->request = new Parameters($post);
    }

    /**
     * setQuery
     *
     * @param array $get
     * @param array $server
     *
     * @return void
     */
    protected function setQuery(array $get)
    {
        if ($this->server->has('QUERY_STRING')) {
            parse_str($this->server->get('QUERY_STRING'), $params);
            $get = array_merge($get, $params);
        }

        $this->query = new Parameters($get);
    }

    /**
     * findMethod
     *
     * @return void
     */
    protected function findRequestMethod()
    {
        if (null !== ($method = $this->headers->get('X_HTTP_METHOD_OVERRIDE'))) {
            return current($method);
        }

        if (null !== ($method = $this->request->get('_method'))) {
            return $method;
        }

        return $this->server->get('REQUEST_METHOD', 'GET');
    }

    /**
     * findHttpVersion
     *
     * @return string
     */
    protected function findHttpVersion()
    {
        if ($this->server->has('SERVER_PROTOCOL')) {
            list (, $version) = explode('/', $this->server->get('SERVER_PROTOCOL'));

            return $version;
        }

        return  '1.1';
    }

    /**
     * findRequestUri
     *
     * @access protected
     * @return string
     */
    protected function findRequestPath()
    {
        $uri = $this->getRequestUri();

        if (0 !== strlen($uri) && strlen(trim($basePath = $this->getBasePath(), '/')) > 0) {
            $base = preg_split('~/~', $basePath, -1, PREG_SPLIT_NO_EMPTY);
            $parts = explode('/', ltrim($uri, '/'));
            $uri = '/' . implode('/', array_splice($parts, count($base)));
        }

        return rtrim($uri, '/');
    }

    /**
     * findBaseUrl
     *
     * @return string
     */
    protected function findBaseUrl()
    {
        $fileName = basename($file = $this->server->get('SCRIPT_FILENAME'));

        if ($fileName === basename($baseUrl = $this->server->get('SCRIPT_NAME')) ||
            $fileName === basename($baseUrl = $this->server->get('PHP_SELF')) ||
            $fileName === basename($baseUrl = $this->server->get('ORIG_SCRIPT_NAME'))
        ) {
            return $baseUrl;
        }

        $segments = explode('/', trim($file, '/'));
        $length = count($segments);
        $path = $this->server->get('PHP_SELF');
        $baseUrl = '';

        do {
            $segment = array_pop($segments);
            $baseUrl = '/'.$segment.$baseUrl;
            $length--;
        } while ($length > 0 && ('' === $baseUrl || false !== $pos = strpos($path, $baseUrl) && 0 !== $pos));

        return $baseUrl;
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
                'HTTP_HOST'            => 'localhost',
                'HTTP_USER_AGENT'      => 'lucid/1.0',
                'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'HTTP_ACCEPT_LANGUAGE' => 'en-US;q=0.6,en;q=0.4',
                'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
                'REMOTE_ADDR'          => '127.0.0.1',
                'SCRIPT_NAME'          => '',
                'SCRIPT_FILENAME'      => '',
                'SERVER_PROTOCOL'      => 'http/1.1',
                'REQEST_METHOD'        => 'GET',
                'REQEST_TIME_FLOAT'    => $time,
                'REQEST_TIME'          => (int)$time,
                'QUERY_STRING'         => '',
            ],
            $server
        );
    }
}
