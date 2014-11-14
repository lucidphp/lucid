<?php

/*
 * This File is part of the Lucid\Module\Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing;

use SplStack;
use Lucid\Module\Routing\Exception\MatchException;
use Lucid\Module\Routing\Handler\HandlerDispatcher;
use Lucid\Module\Routing\Http\RequestContext;
use Lucid\Module\Routing\Http\RequestContextInterface;
use Lucid\Module\Routing\Http\ResponseMapperInterface;
use Lucid\Module\Routing\Http\UrlGeneratorInterface;
use Lucid\Module\Routing\Http\UrlGenerator;
use Lucid\Module\Routing\Http\PassResponseMapper;
use Lucid\Module\Routing\Matcher\MatchContext;
use Lucid\Module\Routing\Matcher\RequestMatcher;
use Lucid\Module\Routing\Matcher\RequestMatcherInterface;
use Lucid\Module\Routing\Handler\HandlerDispatcherInterface;
use Lucid\Module\Routing\Matcher\MatchContextInterface;

/**
 * @class Router
 *
 * @package Lucid\Module\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Router implements RouterInterface
{
    private $matcher;
    private $handlers;
    private $response;
    private $routes;
    private $routeStack;
    private $generator;

    /**
     * Constructor.
     *
     * A response mapper can be used to alter the dispatch result output.
     * The default mapper will simple pass the input value.
     *
     * @param RequestMatcherInterface $matcher
     * @param HandlerDispatcherInterface $handlers
     * @param ResponseMapperInterface $response
     */
    public function __construct(
        RouteCollectionInterface $routes,
        RequestMatcherInterface $matcher = null,
        HandlerDispatcherInterface $handlers = null,
        ResponseMapperInterface $response = null,
        UrlGeneratorInterface $url = null
    ) {
        $this->routes     = $routes;
        $this->matcher    = $matcher ?: new RequestMatcher;
        $this->handlers   = $handlers ?: new HandlerDispatcher;
        $this->response   = $response ?: new PassResponseMapper;
        $this->generator  = $url;
        $this->routeStack = new SplStack;
    }

    /**
     * dispatch
     *
     * @param RequestContextInterface $request
     * @param int $behavior
     *
     * @return mixed
     */
    public function dispatch(RequestContextInterface $request)
    {
        $context = $this->matcher->matchRequest($request, $this->routes);

        if (!$context->isMatch()) {
            throw MatchException::noRouteMatch($request);
        }

        return $this->doDispatch($request, $context);
    }

    /**
     * dispatchRoute
     *
     * @param mixed $name
     * @param array $parameters
     *
     * @return void
     */
    public function dispatchRoute($name, array $parameters = [], array $options = [])
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
        } catch (\InvalidArgumentException $e) {
            throw $e;
        }

        if (null !== $r) {
            $this->getGenerator()->setRequestContext($r);
        }

        $context = $this->createMatchContextFromParameters($parameters, $name, $url);

        return $this->doDispatch($request, $context);
    }

    /**
     * getCurrentRouteName
     *
     * @return RouteInterface|null
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
     * getCurrentRouteName
     *
     * @return string|null
     */
    public function getFirstRouteName()
    {
        if (0 < $this->routeStack->count()) {
            return $this->routeStack->bottom();
        }
    }

    /**
     * getCurrentRoute
     *
     * @return RouteInterface|null
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
     * getCurrentRouteName
     *
     * @return string|null
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
        if (null === $this->generator) {
            $this->generator = new UrlGenerator($this->routes);
        }

        return $this->generator;
    }

    /**
     * doDispatch
     *
     * @param RequestContextInterface $request
     * @param MatchContextInterace $context
     *
     * @return mixed
     */
    protected function doDispatch(RequestContextInterface $request, MatchContextInterface $context)
    {
        $previous = $this->getGenerator()->getRequestContext();

        $this->getGenerator()->setRequestContext($request);
        $this->routeStack->push($context->getName());

        $response = $this->response->mapResponse($this->handlers->dispatchHandler($context));

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
    protected function createRequestContextFromOptions(array $options)
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
    protected function createMatchContextFromParameters(array $parameters, $name, $url)
    {
        return new MatchContext(
            RequestMatcherInterface::MATCH,
            $name,
            $url,
            $this->routes->get($name)->getHandler(),
            $parameters
        );
    }

    protected function getOptions(array $options)
    {
        return array_merge([
            'method' => 'GET',
            'host' => 'localhost',
            'port' => 80,
            'query' => '',
            'scheme' => 'http',
            'base_path' => ''
        ], $options);
    }
}
