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

use Lucid\Module\Routing\RouteInterface;
use Lucid\Module\Routing\RouteContextInterface;
use Lucid\Module\Routing\RouteCollectionInterface;

/**
 * @class UrlGenerator
 *
 * @package Lucid\Module\Routing
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


    private $routes;
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
            return $this->getSchemeAndHost($this->getRequest()).$rel;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, array $parameters = [], $host = null, $type = self::RELATIVE_PATH)
    {
        if (null === $this->routes || !$route = $this->routes->get($name)) {
            throw new \InvalidArgumentException(sprintf('A route with name "%s" could not be found', $name));
        }

        return $this->compilePath($route, $parameters, $host, $type, $name);
    }

    private function getRequest()
    {
        return null === $this->request ? $this->request = new RequestContext : $this->request;
    }

    /**
     * compilePath
     *
     * @param RouteInterface $route
     * @param array $parameters
     * @param mixed $host
     * @param mixed $type
     *
     * @return void
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

        if (!(bool)$context->getParameters()) {
            $url = $prefix.$route->getPattern();

            return 1 === strlen($url) ? $url : rtrim($url, '/');
        }

        $parts      = [];
        $parameters = $this->getRouteParameters($context, $parameters);

        foreach ($context->getTokens() as $token) {

            if ($token->isVariable()) {

                if ($token->isRequired() && !isset($parameters[$token->getValue()])) {
                    throw new \InvalidArgumentException(sprintf('{%s} is a required parameter.', $token->getValue()));
                }

                $parts[] = $parameters[$token->getValue()];
                $parts[] = $token->getSeparator();

            } elseif ($token->isText()) {
                $parts[] = $token->getValue();
            }
        }

        $uri = strtr(rawurlencode(implode('', array_reverse($parts))), static::$noEncode);

        return '/'.trim($prefix.$uri, '/');
    }

    /**
     * getRouteParameters
     *
     * @param RouteContextInterface $context
     * @param array $parameters
     *
     * @return array
     */
    private function getRouteParameters(RouteContextInterface $context, array $parameters)
    {
        return array_merge(
            array_combine(array_values($v = $context->getParameters()), array_fill(1, count($v), null)),
            $parameters
        );
    }

    /**
     * varMatchesRequirement
     *
     * @param array $token
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     * @return boolean
     */
    private function varMatchesRequirement(array $token, array $parameters)
    {
        if ((bool)preg_match($regexp = '#^'.$token[2].'$#', $param = $parameters[$token[3]])) {
            return true;
        }

        return false;
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
    private function getPathPrefix(RouteInterface $route, $host)
    {
        $context = $route->getContext();

        if (null === $route->getHost()) {
            $host = $this->request->getHost();
        } elseif (null === $host) {
            throw new \InvalidArgumentException('Route requires host, no host given.');
        } elseif (null !== $host && !(bool)preg_match($context->getHostRegexp(), $host)) {
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
    private function getSchemeAndHost()
    {
        $port = $req->getPort();
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
        return $req->getBaseUrl().$req->getPath();
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
}
