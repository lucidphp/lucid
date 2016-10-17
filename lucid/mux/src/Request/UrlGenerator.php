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

use Lucid\Mux\Parser\Token;
use Lucid\Mux\RouteInterface;
use Lucid\Mux\RouteContextInterface;
use Lucid\Mux\Request\ContextInterface as RequestContextInterface;
use Lucid\Mux\Request\Context as RequestContext;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Parser\Variable;

/**
 * @class UrlGenerator
 *
 * @package Lucid\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * noEncode
     *
     * @var array
     */
    private static $noEncode = [
        '%2F' => '/',
        '%40' => '@',
        '%3A' => ':',
        '%3B' => ';',
        '%2C' => ',',
        '%3D' => '=',
        '%2B' => '+',
        '%21' => '!',
        '%2A' => '*',
        '%7C' => '|',
    ];

    /**
     * routes
     *
     * @var RouteCollectionInterface
     */
    private $routes;

    /**
     * request
     *
     * @var RequestContextInterface
     */
    private $request;

    /**
     * Constructor.
     *
     * @param RouteCollectionInterface $routes
     * @param RequestContextInterface $request
     *
     */
    public function __construct(RouteCollectionInterface $routes = null, RequestContextInterface $request = null)
    {
        $this->routes = $routes;
        $this->setRequestContext($request ?: new RequestContext);
    }

    /**
     * setRequestContext
     *
     * @param RequestContextInterface $request
     *
     * @return void
     */
    public function setRequestContext(RequestContextInterface $request) : void
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestContext() : RequestContextInterface
    {
        return $this->request;
    }

    /**
     * setRoutes
     *
     * @param RouteCollectionInterface $routes
     *
     * @return void
     */
    public function setRoutes(RouteCollectionInterface $routes) : void
    {
        $this->routes = $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function currentUrl(int $type = self::RELATIVE_PATH) : ?string
    {
        if (null !== ($path = $this->currentPath($type))) {
            return $path . $this->getQueryString($this->getRequest());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function currentPath(int $type = self::RELATIVE_PATH) : ?string
    {
        $rel = $this->getReqPath($this->getRequest());

        if ($type === self::RELATIVE_PATH) {
            return $rel;
        } elseif ($type === self::ABSOLUTE_PATH) {
            return $this->getSchemeAndHost($this->getRequest()).'/'.trim($rel, '/');
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(
        string $name,
        array $parameters = [],
        string $host = null,
        int $type = self::RELATIVE_PATH
    ) : string {
        if (null === $this->routes || !$this->routes->has($name)) {
            throw new \InvalidArgumentException(sprintf('A route with name "%s" could not be found.', $name));
        }

        $route = $this->routes->get($name);

        return $this->compilePath($route, $parameters, $host ?: $route->getHost(), $type, $name);
    }

    /**
     * Get the current request context.
     *
     * Gets the current set `RequestContext` object or creates a new
     * instance of `RequestContext`
     *
     * @return RequestContextInterface
     */
    private function getRequest() : RequestContextInterface
    {
        return null === $this->request ? $this->request = new RequestContext : $this->request;
    }

    /**
     * Compiles a Route instance into a readable path or url.
     *
     * @param RouteInterface $route the route
     * @param array          $parameters route parameters
     * @param string         $host the host name.
     * @param int            $type the path type
     * @param string         $name the route name
     *
     * @throws \InvalidArgumentException if `$route` requires a `$host` and
     * none is given.
     * @throws \InvalidArgumentException if a required parameter by `$route` is
     * amiss.
     *
     * @return string
     */
    private function compilePath(
        RouteInterface $route,
        array $parameters,
        ?string $host,
        int $type,
        string $name
    ) : string {
        if ($this->isRelPath($type) && null !== $route->getHost()) {
            throw new \InvalidArgumentException(
                sprintf('Can\'t create relative path because route "%s" requires a dedicated hostname.', $name)
            );
        }

        $context = $route->getContext();
        $prefix  = $this->isAbsPath($type) ? $this->getPathPrefix($route, $host) : '';

        if (0 === count($vars = $context->getVars())) {
            return $this->getPrefixed($context->getStaticPath(), $prefix);
        }

        $vars  = $this->getRouteVars($context, $parameters);
        $parts = array_map(function (Token $token) use ($vars) {
            if (!($token instanceof Variable)) {
                return $token->value();
            }

            if ($token->isRequired() && !isset($vars[$token->value()])) {
                throw new \InvalidArgumentException(sprintf('{%s} is a required parameter.', $token->value()));
            }

            return $vars[$token->value()];
        }, $context->getTokens());

        return $this->getPrefixed(implode('', $parts), $prefix);
    }

    /**
     * @param int|null $type
     * @return bool
     */
    private function isAbsPath(int $type = null) : bool
    {
        return static::ABSOLUTE_PATH === $type;
    }

    /**
     * @param int|null $type
     * @return bool
     */
    private function isRelPath(int $type = null) : bool
    {
        return static::RELATIVE_PATH === $type;
    }

    /**
     * getRouteVars
     *
     * @param RouteContextInterface $context
     * @param array $parameters
     *
     * @return array
     */
    private function getRouteVars(RouteContextInterface $context, array $parameters) : array
    {
        return array_merge(
            array_combine(array_values($v = $context->getVars()), array_fill(1, count($v), null)),
            $parameters
        );
    }

    /**
     * getPathPrefix
     * @param RouteInterface $route
     * @param string $host
     *
     * @return string
     */
    private function getPathPrefix(RouteInterface $route, string $host = null) : string
    {
        if (null === $host) {
            $host = $route->getHost() ? $route->getHost() : $this->request->getHost();
        }

        if (null !== $route->getHost() && !(bool)preg_match($route->getContext()->getHostRegex(), $host)) {
            throw new \InvalidArgumentException('Host requirement does not match given host.');
        }

        return sprintf('%s://%s', $this->getRouteProtocol($route, $this->getRequest()), $host);
    }

    /**
     * getRouteProtocol
     *
     * @param RouteInterface $route
     * @param RequestContextInterface $request
     *
     * @return string
     */
    private function getRouteProtocol(RouteInterface $route, RequestContextInterface $request)
    {
        $requestScheme = $request->getScheme();

        $schemes = $route->getSchemes();

        if (in_array($requestScheme, $schemes)) {
            return $requestScheme;
        }

        return current($schemes) ?: 'http';
    }

    /**
     * getSchemeAndHost
     *
     * @param RequestContextInterface $req
     *
     * @return string
     */
    private function getSchemeAndHost(RequestContextInterface $req) : string
    {
        $port = $req->getHttpPort();
        $host = in_array($port, [80, 443]) ? $req->getHost() : $req->getHost() . ':' . $port;

        return $req->getScheme() . '://' . $host;
    }

    /**
     * getReqPath
     *
     * @param RequestContextInterface $req
     *
     * @return string
     */
    private function getReqPath(RequestContextInterface $req) : string
    {
        return $req->getPath();
    }

    /**
     * getPathAndQuery
     *
     * @param RequestContextInterface $req
     *
     * @return string
     */
    private function getPathAndQuery(RequestContextInterface $req) : string
    {
        return $this->getReqPath($req).$this->getQueryString($req);
    }

    /**
     * getQueryString
     *
     * @param RequestContextInterface $req
     *
     * @return string
     */
    private function getQueryString(RequestContextInterface $req) : string
    {
        return $req->getQueryString() ? '?'.$req->getQueryString() : '';
    }

    /**
     * getPrefied
     *
     * @param string $path
     * @param string $prefix
     *
     * @return string
     */
    private function getPrefixed(string $path, string $prefix) : string
    {
        return $prefix.'/'.trim(strtr(rawurlencode($path), static::$noEncode), '/');
    }
}
