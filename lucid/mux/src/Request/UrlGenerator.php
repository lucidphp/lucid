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

use Lucid\Mux\RouteInterface;
use Lucid\Mux\RouterInterface;
use Lucid\Mux\RouteContextInterface;
use Lucid\Mux\Request\ContextInterface as RequestContextInterface;
use Lucid\Mux\Request\Context as RequestContext;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Parser\Text;
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
     * @return void
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
    public function setRequestContext(RequestContextInterface $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestContext()
    {
        return $this->request;
    }

    /**
     * setRoutes
     *
     * @param RouteCollectionInterface $request
     *
     * @return void
     */
    public function setRoutes(RouteCollectionInterface $routes)
    {
        $this->routes = $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function currentUrl($type = self::RELATIVE_PATH)
    {
        if (null !== ($path = $this->currentPath($type))) {
            return $path . $this->getQueryString($this->getRequest());
        }

        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function currentPath($type = self::RELATIVE_PATH)
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
    public function generate($name, array $parameters = [], $host = null, $type = self::RELATIVE_PATH)
    {
        if (null === $this->routes || !$route = $this->routes->get($name)) {
            throw new \InvalidArgumentException(sprintf('A route with name "%s" could not be found.', $name));
        }

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
    private function getRequest()
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
     *
     * @throws \InvalidArgumentException if `$route` requires a `$host` and
     * none is given.
     * @throws \InvalidArgumentException if a required parameter by `$route` is
     * amiss.
     *
     * @return string
     */
    private function compilePath(RouteInterface $route, array $parameters, $host, $type, $name)
    {
        if (static::RELATIVE_PATH === $type && null !== $route->getHost()) {
            throw new \InvalidArgumentException(
                sprintf('Can\'t create relative path because route "%s" requires a deticated hostname.', $name)
            );
        }

        $context = $route->getContext();
        $prefix  = static::ABSOLUTE_PATH === $type ? $this->getPathPrefix($route, $host) : '';

        if (0 === count($vars = $context->getVars())) {
            return $this->getPrefixed($context->getStaticPath(), $prefix);
        }

        $parts      = [];
        $vars = $this->getRouteVars($context, $parameters);

        foreach ($context->getTokens() as $token) {
            if ($token instanceof Variable) {
                if ($token->required && !isset($vars[$token->value])) {
                    throw new \InvalidArgumentException(sprintf('{%s} is a required parameter.', $token->getValue()));
                }
                $parts[] = $vars[$token->value];
            } else {
                $parts[] = $token->value;
            }
        }

        return $this->getPrefixed(implode('', $parts), $prefix);
    }

    /**
     * getRouteVars
     *
     * @param RouteContextInterface $context
     * @param array $parameters
     *
     * @return array
     */
    private function getRouteVars(RouteContextInterface $context, array $parameters)
    {
        return array_merge(
            array_combine(array_values($v = $context->getVars()), array_fill(1, count($v), null)),
            $parameters
        );
    }

    /**
     * getPathPrefix
     *
     * @param RouteInterface $route
     * @param array $parameters
     * @param mixed $host
     *
     * @return mixed
     */
    private function getPathPrefix(RouteInterface $route, $host = null)
    {
        $context = $route->getContext();

        if (null === $host) {
            $host = $route->getHost() ? $route->getHost() : $this->request->getHost();
        }

        if (null !== $route->getHost() && !(bool)preg_match($context->getHostRegex(), $host)) {
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
     * @return string
     */
    private function getSchemeAndHost($req)
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
    private function getReqPath(RequestContextInterface $req)
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
    private function getPathAndQuery(RequestContextInterface $req)
    {
        return $req->getReqPath($req).$this->getQueryString($req);
    }

    /**
     * getQueryString
     *
     * @param RequestContextInterface $req
     *
     * @return string
     */
    private function getQueryString(RequestContextInterface $req)
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
    private function getPrefixed($path, $prefix)
    {
        return $prefix.'/'.trim(strtr(rawurlencode($path), static::$noEncode), '/');
    }
}
