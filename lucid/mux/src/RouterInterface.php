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

use Lucid\Mux\Exception\MatchException;
use Lucid\Mux\Matcher\ContextInterface as MatchContext;
use Lucid\Mux\Request\ContextInterface as RequestContext;

/**
 * @interface MultiplexerInterface
 *
 * @package Lucid\Mux
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouterInterface
{
    /** @var int */
    const R_PORT           = 80;

    /** @var string */
    const R_HOST           = 'localhost';

    /** @var string */
    const R_METHOD         = 'GET';

    /** @var string */
    const R_QUERY          = '';

    /** @var string */
    const R_SCHEME_DEFAULT = 'http';

    /** @var string */
    const R_SCHEME_SECURE  = 'https';

    /**
     * dispatch
     *
     * @param RequestContext $context
     *
     * @throws MatchException
     *
     * @return mixed the request response.
     */
    public function dispatch(RequestContext $context);

    /**
     * Dispatches a match.
     *
     * @param MatchContext $match
     *
     * @return mixed the request response.
     */
    public function dispatchMatch(MatchContext $match);

    /**
     * match
     *
     * @param RequestContext $context
     *
     * @return MatchContext
     */
    public function match(RequestContext $context) : MatchContext;

    /**
     * Routes to a named route.
     *
     * @param string $name
     * @param array $parameters
     * @param array $options
     * @return mixed
     */
    public function route(string $name, array $parameters = [], array $options = []);

    /**
     * Get the first route that's been dispatched.
     *
     * @return RouteInterface the route object, `NULL` if none.
     */
    public function getFirstRoute() : ?RouteInterface;

    /**
     * Get the first route name that's been dispatched.
     *
     * @return string the route name, `NULL` if none.
     */
    public function getFirstRouteName() : ?string;

    /**
     * Get the current dispatched route.
     *
     * @return RouteInterface a route object.
     */
    public function getCurrentRoute() : ?RouteInterface;

    /**
     * Get the current dispatched route name.
     *
     * @return string the route name.
     */
    public function getCurrentRouteName() : ?string;

    /**
     * Generates a http url for a given route name.
     *
     * @param string $name
     * @param string $host
     * @param array $vars
     * @param bool $rel
     *
     * @return string
     */
    public function getUrl($name, $host = null, array $vars = [], $rel = true) : string;
}
