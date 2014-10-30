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
use Lucid\Module\Routing\Http\RequestContextInterface;
use Lucid\Module\Routing\Handler\HandlerParser;
use Lucid\Module\Routing\Handler\HandlerDispatcher;
use Lucid\Module\Routing\Handler\ParameterMapperInterface;
use Lucid\Module\Routing\Http\ResponseMapperInterface as Mapper;
use Lucid\Module\Routing\Matcher\RequestMatcherInterface as Matcher;
use Lucid\Module\Routing\Handler\HandlerDispatcherInterface as Dispatcher;
use Lucid\Module\Routing\Http\NullResponseMapper;
use Lucid\Module\Routing\Exception\MatchException;
use Lucid\Module\Routing\Http\UrlGeneratorInterface as Url;
use Lucid\Module\Routing\Http\UrlGenerator;
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
    private $generator;

    /**
     * Constructor.
     *
     * @param RequestMatcherInterface $matcher
     * @param HandlerDispatcherInterface $handlers
     * @param ResponseMapperInterface $response
     */
    public function __construct(Matcher $matcher, Dispatcher $handlers = null, Url $url = null, Mapper $response = null)
    {
        $this->matcher   = $matcher;
        $this->handlers  = $handlers ?: new HandlerDispatcher;
        $this->response  = $response ?: new NullResponseMapper;
        $this->generator = $url;

        $this->routes    = new SplStack;
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
        $context = $this->matcher->matchRequest($request);

        if (!$context->isMatch()) {
            throw MatchException::noRouteMatch($request);
        }

        return $this->doDispatch($request, $context);
    }

    /**
     * getCurrentRoute
     *
     * @return RouteInterface|null
     */
    public function getCurrentRoute()
    {
        if (null === $name = $this->getCurrentRouteName()) {
            return;
        }

        if ($this->matcher->getRoutes()->has($name)) {
            return $this->matcher->getRoutes()->get($name);
        }
    }

    /**
     * getCurrentRouteName
     *
     * @return string|null
     */
    public function getCurrentRouteName()
    {
        if (0 < $this->routes->count()) {
            return $this->routes->top();
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
            $this->generator = new UrlGenerator($this->matcher->getRoutes());
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
        $this->getGenerator()->setRequestContext($request);
        $this->routes->push($context->getName());

        $response = $this->response->mapResponse($this->handlers->dispatchHandler($context));

        $this->routes->pop();

        return $response;
    }

}
