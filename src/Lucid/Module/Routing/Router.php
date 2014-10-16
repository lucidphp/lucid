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
use Lucid\Module\Routing\Matcher\RequestMatcherInterface;
use Lucid\Module\Routing\Handler\HandlerParser;
use Lucid\Module\Routing\Handler\HandlerDispatcher;
use Lucid\Module\Routing\Handler\HandlerDispatcherInterface;
use Lucid\Module\Routing\Http\GenericResponseMapper;
use Lucid\Module\Routing\Http\ResponseMapperInterface;
use Lucid\Module\Routing\Handler\ParameterMapperInterface;

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

    /**
     * Constructor.
     *
     * @param RequestMatcherInterface $matcher
     * @param ResponseMapperInterface $response
     * @param HandlerDispatcherInterface $handlers
     */
    public function __construct(
        RequestMatcherInterface $matcher,
        ResponseMapperInterface $response = null,
        HandlerDispatcherInterface $handlers = null
    ) {
        $this->matcher = $matcher;
        $this->handlers = $handlers ?: new HandlerDispatcher;
        $this->response = $response ?: new GenericResponseMapper;
        $this->routes = new SplStack;
    }

    /**
     * dispatch
     *
     * @param RequestContextInterface $request
     * @param int $behavior
     *
     * @return
     */
    public function dispatch(RequestContextInterface $request, $behavior = self::TRANS_EMPTY_RESULT)
    {
        list ($stat, $context) = $this->matcher->matchRequest($request);

        $this->routes->push($context->getName());

        if (RequestMatcherInterface::MATCH === $stat) {
            $status = 200;
            $result = $this->handlers->dispatchHandler($context);
        } else {
            $status = 404;
            $result = '';
        }

        $response = $this->response->mapResponse($result, $context, $status);
        $this->routes->pop();

        return $response;
    }
}
