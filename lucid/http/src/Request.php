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

use Lucid\Http\Request\Body;
use Lucid\Http\Request\Files;
use Lucid\Http\Request\Server;
use Lucid\Http\Request\Headers;
use Lucid\Http\Cookie\Cookies;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @class Request
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Request implements RequestInterface
{
    private $version;
    private $url;
    private $baseUrl;
    private $pathInfo;
    private $host;
    private $port;
    private $method;

    /** @var \Psr\Http\Message\UriInterface */
    private $uri;

    /** @var \Psr\Http\Message\StreamInterface */
    private $body;

    /** @var \Lucid\Http\ParameterInterface */
    private $request;

    /** @var \Lucid\Http\ParameterInterface */
    private $query;

    /** @var \Lucid\Http\Request\ServerInterface */
    public $server;

    /** @var \Lucid\Http\Request\UploadedFilesInterface */
    public $files;

    /** @var \Lucid\Http\Cookie\CookieJarInterface */
    public $cookies;

    /** @var \Lucid\Http\ParameterInterface */
    public $headers;

    /** @var \Lucid\Http\ParameterMutableInterface */
    public $attributes;

    /** @var \Lucid\Http\ParameterMutableInterface */
    public $session;

    /** @var string */
    private $content;

    private $queryString;

    /**
     * Constructor
     *
     * @param array $query
     * @param array $request
     * @param array $attributes
     * @param array $files
     * @param array $cookies
     * @param array $server
     * @param string $content
     *
     * @return void
     */
    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $server = [],
        array $files = [],
        array $cookies = [],
        $content = null
    ) {

        $this->server = new Server($server);
        $this->setQuery($query);
        $this->request = new Parameters($request);
        $this->attributes = new ParametersMutable($attributes);
        $this->headers = new Headers($server->getHeaders());
        $this->cookies = new Cookies($cookies);
        $this->files = new Files($files);

        $this->content = (string)$content;
    }

    /**
     * Clones this request object.
     *
     * @return void
     */
    public function __clone()
    {
        foreach (['headers', 'files', 'cookies', 'attributes', 'server', 'session'] as $prop) {
            $this->{$prop} = clone $this->{$prop};
        }

        if (null !== $this->body) {
            $this->body = clone $this->body;
        }
    }

    /**
     * create
     *
     * @param string $method
     * @param string $path
     *
     * @return void
     */
    public static function create(
        $path = '/',
        $method = 'GET',
        $queryString = '',
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        $content = null
    ) {
        return new self(parse_str($queryString), $request, $attributes, static::prepareRequest($path, $method), $files, $cookies, $content);
    }
    /**
     * Creates a new request from super globals
     *
     * @return RequestInterface
     */
    public static function createFromGlobals()
    {
        return new static($_GET, $_POST, [], $_SERVER, $_FILES, $_COOKIE);
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        if (null !== $this->version) {
            return $this->version;
        }

        if ($this->server->has('SERVER_PROTOCOL')) {
            list (, $version) = explode('/', $this->server->get('SERVER_PROTOCOL'));
        } else {
            $version = '1.1';
        }

        return $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version)
    {
        $request = clone $this;
        $request->server->set('SERVER_PROTOCOL', 'http/'.$version);
        $request->version = null;

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name)
    {
        $lines = [];

        foreach (preg_split('~,~', $name, -1, PREG_SPLIT_NO_EMPTY) as $key) {
            if ($this->headers->has($key)) {
                $lines[] = $this->getHeader($key);
            }
        }

        return implode(',', $lines);
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
    public function getBody()
    {
        if (null !== $this->body) {
            return $this->body;
        }

        if (null !== $this->content) {
            $body = Body::createFromString($this->content);
        } else {
            $body = Body::createFromInput(
                0 === strcmp('multipart/form-data', $this->server->get('CONTENT_TYPE'))
            );
        }

        return $this->body = $body;
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
    public function withMethod($method)
    {
        $this->isValidRequestMethod($method);

        $request = clone $this;
        $request->method = null;
        $request->server->set('REQUEST_METHOD', strtoupper($method));

        // TODO: replace x-http-requested-with headers
        if ($request->server->has('x-http-requested-with')) {
        }

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        if (null === $this->url) {
            $this->url = $this->findBaseUrl() . (0 === strcmp($info = $this->getPathInfo(), '/') ? '' : $info);
        }

        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri()
    {
        return null === $this->uri ? $this->uri = Url::fromString($this->findRequestUri()) : $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->findPathInfo();
        }

        return $this->pathInfo;
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
    public function getRequestParams()
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
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {
        return (string)$this->getUri();
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return self
     */
    public function withRequestTarget($requestTarget)
    {
    }


    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return self
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
    }


    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        $headers = $this->headers->all();
        $headers[$name] = $value;

        $request = clone $this;
        $request->headers = new Headers($headers);

        return $request;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value)
    {
        $headers = $this->headers->all();

        if (isset($headers[$name])) {
            $headers[$name][] = $value;
        } else {
            $headers[$name] = $value;
        }

        $request = clone $this;
        $request->headers = new Headers($headers);

        return $request;
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return self
     */
    public function withoutHeader($name)
    {
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return self
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body)
    {
        $request = clone $this;
        $request->content = null;
        $request->body = $body;

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {
        $request = clone $this;
        $request->cookies = new Cookies($cookies);

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $request = clone $this;
        $request->query = new Parameters($query);

        return $request;
    }

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     */
    public function getUploadedFiles()
    {
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array An array tree of UploadedFileInterface instances.
     * @return self
     * @throws \InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
    }

    /**
     * Return an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     * @return self
     * @throws \InvalidArgumentException if an unsupported argument type is
     *     provided.
     */
    public function withParsedBody($data)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value)
    {
        $request = clone $this;
        $request->attributes->set($name, $value);

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name)
    {
        $request = clone $this;
        $request->attributes->delete($name);

        return $request;
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
    }

    /**
     * setHeaders
     *
     * @param Server $server
     *
     * @return void
     */
    protected function setHeaders(ServerInterface $server)
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
    private function setQuery(array $get)
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
    private function findHttpVersion()
    {
        if ($this->server->has('SERVER_PROTOCOL')) {
            list (, $version) = explode('/', $this->server->get('SERVER_PROTOCOL'));

            return $version;
        }

        return  '1.1';
    }

    /**
     * @return string
     */
    protected function findPathInfo()
    {
        $uri = $this->server->get('REQUEST_URI');
    }

    protected function findBasePath()
    {
        $path = '';
        $fileName = basename($file = $this->server->get('SCRIPT_FILENAME'));

        if (0 < strlen($baseUrl = str_replace('\\', '/', $this->findBaseUrl()))) {
            return $path;
        }

        if ($fileName === basename($baseUrl)) {
            return $basePath = dirname($baseUrl);
        }
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

        $path = $this->server->get('PHP_SELF', '');
        $segments = explode('/', trim($file, '/'));
        $length = count($segments);
        $baseUrl = '';

        do {
            $segment = array_pop($segments);
            $baseUrl = '/'.$segment.$baseUrl;
            $length--;
        } while ($length > 0 && ('' === $baseUrl || false !== $pos = strpos($path, $baseUrl) && 0 !== $pos));

        return $baseUrl;
    }

    /**
     * findRequestUri
     *
     * @return string
     */
    protected function findRequestUri()
    {
        if (null === ($uri = $this->server->get('REDIRECT_URL'))) {
            $uri = current(explode('?', $this->server->get('REQUEST_URI')));
        }

        return $uri;
    }
}
