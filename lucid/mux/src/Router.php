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
use InvalidArgumentException;
use Lucid\Mux\Request\UrlGenerator;
use Lucid\Mux\Request\UrlGeneratorInterface;
use Lucid\Mux\Handler\HandlerDispatcher;
use Lucid\Mux\Handler\HandlerDispatcherInterface;
use Lucid\Mux\Matcher\ContextInterface as MatchContextInterface;
use Lucid\Mux\Matcher\Context as MatchContext;
use Lucid\Mux\Request\ContextInterface as RequestContextInterface;
use Lucid\Mux\Request\Context as RequestContext;

/**
 * @class Router
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Router implements MultiplexerInterface
{
    /**
     * routes
     *
     * @var RouteCollectionInterface
     */
    private $routes;

    /**
     * builder
     *
     * @var mixed
     */
    private $matcher;

    /**
     * dispatcher
     *
     * @var HandlerDispatcherInterface
     */
    private $dispatcher;

    /**
     * mapper
     *
     * @var ResponseMapperInterface
     */
    private $mapper;

    /**
     * url
     *
     * @var UrlGeneratorInterface
     */
    private $url;

    /**
     * routeStack
     *
     * @var SplStack
     */
    private $routeStack;

    /**
     * Constructor.
     *
     * @param RouteCollectionInterface $routes
     * @param RequestMatcherInterface $matcher
     * @param HandlerDispatcherInterface $dispatcher
     * @param ResponseMapperInterface $mapper
     * @param UrlGeneratorInterface $url
     */
    public function __construct(
        RouteCollectionInterface $routes,
        RequestMatcherInterface $matcher = null,
        HandlerDispatcherInterface $dispatcher = null,
        ResponseMapperInterface $mapper = null,
        UrlGeneratorInterface $url = null
    ) {
        $this->routes = $routes;
        $this->matcher = $matcher ?: new RequestMatcher;
        $this->dispatcher = $dispatcher ?: new HandlerDispatcher;
        $this->mapper = $mapper ?: new PassResponseMapper;
        $this->url = $url ?: new UrlGenerator;
        $this->routeStack = new SplStack;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestContextInterface $request)
    {
        if (($match = $this->matcher->matchRequest($request, $this->routes)) && $match->isMatch()) {
            return $this->dispatchRequest($request, $match);
        }

        throw MatchException::noRouteMatch($request);
    }

    /**
     * {@inheritdoc}
     */
    public function route($name, array $parameters = [], array $options = [])
    {
        $options = $this->getOptions($options);

        $type = 'localhost' === $options['host'] ?
            UrlGeneratorInterface::RELATIVE_PATH :
            UrlGeneratorInterface::ABSOLUTE_PATH;

        $request = $this->createRequestContextFromOptions($options);
        $r = $this->getGenerator()->getRequestContext();
        $this->getGenerator()->setRequestContext($request);

        try {
            $url = $this->getGenerator()->generate($name, $parameters, $options['host'], $type);
        } catch (InvalidArgumentException $e) {
            throw $e;
        }

        if (null !== $r) {
            $this->getGenerator()->setRequestContext($r);
        }

        $context = $this->createMatchContextFromParameters($parameters, $name, $url);

        return $this->dispatchRequest($request, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstRoute()
    {
        if (null === ($name = $this->getFirstRouteName())) {
            return;
        }

        if ($this->routes->has($name)) {
            return $this->routes->get($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstRouteName()
    {
        if (0 < $this->routeStack->count()) {
            return $this->routeStack->bottom();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentRoute()
    {
        if (null === ($name = $this->getCurrentRouteName())) {
            return;
        }

        if ($this->routes->has($name)) {
            return $this->routes->get($name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentRouteName()
    {
        if (0 < $this->routeStack->count()) {
            return $this->routeStack->top();
        }
    }

    /**
     * getGenerator
     *
     * @return UrlGeneratorInterface
     */
    public function getGenerator()
    {
        if (null === $this->url) {
            $this->url = new UrlGenerator;
        }

        return $this->url;
    }

    /**
     * Dispatches a request.
     *
     * @param RequestContextInterface $request
     * @param MatchContextInterface $match
     *
     * @return mixed the request response.
     */
    private function dispatchRequest(RequestContextInterface $request, MatchContextInterface $match)
    {
        $previous = $this->getGenerator()->getRequestContext();

        $this->getGenerator()->setRequestContext($request);
        $this->routeStack->push($match->getName());

        $response = $this->response->mapResponse($this->dispatcher->dispatch($match));

        $this->routeStack->pop();

        // restore the previous request context.
        if (null !== $previous) {
            $this->getGenerator()->setRequestContext($previous);
        }

        return $response;
    }

    /**
     * createRequestContextFromOptions
     *
     * @param array $options
     *
     * @return RequestContextInterface
     */
    private function createRequestContextFromOptions(array $options)
    {
        return new RequestContext(
            $options['base_path'],
            '',
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
     * @param array $parameters
     * @param string $url
     *
     * @return MatchContextInterface
     */
    private function createMatchContextFromParameters(array $parameters, $name, $url)
    {
        return new MatchContext(
            RequestMatcherInterface::MATCH,
            $name,
            $url,
            $this->routes->get($name)->getHandler(),
            $parameters
        );
    }

    /**
     * getOptions
     *
     * @param array $options
     *
     * @return array
     */
    private function getOptions(array $options)
    {
        return array_merge([
            'method'    => 'GET',
            'host'      => 'localhost',
            'port'      => 80,
            'query'     => '',
            'scheme'    => 'http',
            'base_path' => ''
        ], $options);
    }
}
