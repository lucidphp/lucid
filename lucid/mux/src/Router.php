<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux;

use SplStack;
use Lucid\Mux\Request\UrlGenerator;
use Lucid\Mux\Matcher\RequestMatcher;
use Lucid\Mux\Exception\MatchException;
use Lucid\Mux\Request\PassResponseMapper;
use Lucid\Mux\Handler\DispatcherInterface;
use Lucid\Mux\Matcher\Context as MatchContext;
use Lucid\Mux\Request\Context as RequestContext;
use Lucid\Mux\Request\UrlGeneratorInterface as Url;
use Lucid\Mux\Handler\Dispatcher as HandlerDispatcher;
use Lucid\Mux\Matcher\RequestMatcherInterface as Matcher;
use Lucid\Mux\Handler\DispatcherInterface as Dispatcher;
use Lucid\Mux\Request\ResponseMapperInterface as ResponseMapper;
use Lucid\Mux\Matcher\ContextInterface as MatchContextInterface;
use Lucid\Mux\Request\ContextInterface as RequestContextInterface;

/**
 * @class Router
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Router implements RouterInterface
{
    /** @var array */
    private const DEFAULT_OPTIONS = [
        'method'    => 'GET',
        'host'      => 'localhost',
        'port'      => 80,
        'query'     => '',
        'scheme'    => 'http'
    ];

    /** @var RouteCollectionInterface */
    private $routes;

    /** @var \Lucid\Mux\Matcher\RequestMatcherInterface */
    private $matcher;

    /** @var DispatcherInterface */
    private $dispatcher;

    /** @var ResponseMapper */
    private $mapper;

    /** @var Url */
    private $url;

    /** @var SplStack */
    private $routeStack;

    /**
     * Router constructor.
     * @param RouteCollectionInterface $routes
     * @param Matcher|null $matcher
     * @param Dispatcher|null $dispatcher
     * @param ResponseMapper|null $mapper
     * @param Url|null $url
     */
    public function __construct(
        RouteCollectionInterface $routes,
        Matcher $matcher = null,
        Dispatcher $dispatcher = null,
        ResponseMapper $mapper = null,
        Url $url = null
    ) {
        $this->routes     = $routes;
        $this->matcher    = $matcher ?: new RequestMatcher;
        $this->dispatcher = $dispatcher ?: new HandlerDispatcher;
        $this->mapper     = $mapper ?: new PassResponseMapper;
        $this->url        = $url ?: new UrlGenerator($this->routes);
        $this->routeStack = new SplStack;
    }

    /**
     * {@inheritdoc}
     */
    public function match(RequestContextInterface $request) : MatchContextInterface
    {
        return $this->matcher->matchRequest($request, $this->routes);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestContextInterface $request)
    {
        if (($match = $this->match($request)) && !$match->isMatch()) {
            throw MatchException::noRouteMatch($request, $match);
        }

        return $this->dispatchMatch($match);
    }

    /**
     * Dispatches a request.
     *
     * @param \Lucid\Mux\Matcher\ContextInterface $match
     *
     * @return mixed the request response.
     */
    public function dispatchMatch(MatchContextInterface $match)
    {
        // store the previous request context.
        $request = $this->url->getRequestContext();

        $this->url->setRequestContext($match->getRequest());
        $this->routeStack->push($match->getName());

        $response = $this->mapper->mapResponse($this->dispatcher->dispatch($match));

        $this->routeStack->pop();

        // restore the previous request context.
        if (null !== $request) {
            $this->url->setRequestContext($request);
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function route(string $name, array $vars = [], array $options = [])
    {
        $opts = array_merge(self::DEFAULT_OPTIONS, $options);

        return $this->dispatchMatch(
            $this->matchContextFromParameters(
                $vars,
                $name,
                $this->requestContextFromOptions(
                    $this->getUrl($name, $opts['host'], $vars, 'localhost' === $opts['host']),
                    $opts
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstRoute() : ?RouteInterface
    {
        if (null === ($name = $this->getFirstRouteName()) || !$this->routes->has($name)) {
            return null;
        }

        return $this->routes->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstRouteName() : ?string
    {
        if (!(bool)$this->routeStack->count()) {
            return null;
        }

        return $this->routeStack->bottom();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentRoute() : ?RouteInterface
    {
        if (null === ($name = $this->getCurrentRouteName()) || !$this->routes->has($name)) {
            return null;
        }

        return $this->routes->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentRouteName() : ?string
    {
        if (!(bool)$this->routeStack->count()) {
            return null;
        }

        return $this->routeStack->top();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($name, $host = null, array $vars = [], $rel = true) : string
    {
        $rel = $rel ? $type = Url::RELATIVE_PATH : Url::ABSOLUTE_PATH;

        return $this->url->generate($name, $vars, $host, $rel);
    }

    /**
     * Creates a RequestContext from an options array
     *
     * @param string $path
     * @param array $options
     *
     * @return RequestContextInterface
     */
    private function requestContextFromOptions(string $path, array $options) : RequestContextInterface
    {
        return new RequestContext(
            $path,
            $options['method'],
            $options['query'],
            $options['host'],
            $options['scheme'],
            $options['port']
        );
    }

    /**
     * createMatchContextFromParameters
     *
     * @param array $vars
     * @param string $name
     * @param \Lucid\Mux\Request\ContextInterface $request
     *
     * @return \Lucid\Mux\Matcher\Context
     */
    private function matchContextFromParameters(
        array $vars,
        string $name,
        RequestContextInterface $request
    ) : MatchContext {
        $handler = $this->routes->get($name)->getHandler();

        return new MatchContext(Matcher::MATCH, $name, $request, $handler, $vars);
    }
}
