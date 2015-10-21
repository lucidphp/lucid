<?php

/*
 * This File is part of the Lucid\Routing\Matcher package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Matcher;

use Lucid\Mux\RouteInterface;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Http\RequestContextInterface;
use Lucid\Mux\Cache\CachedCollectionInterface;

/**
 * @class RequestMatcher
 *
 * @package Lucid\Routing\Matcher
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RequestMatcher implements RequestMatcherInterface
{
    /**
     * matchRequest
     *
     * @param Request $request
     *
     * @return void
     */
    public function matchRequest(RequestContext $context, RouteCollectionInterface $routes)
    {
        $routes = $this->reduce($routes, $context);

        $path = $context->getPath();

        if ($routes instanceof CachedCollectionInterface && 0 !== count($r = $route->findByStaticPath($path))) {
            $routes = $r;
        }

        foreach ($routes->all() as $name => $route) {
            if (null !== ($host = $route->getHost())
                && !preg_match($route->getContext()->getHostRegexp(), $request->getHost())
            ) {
                continue;
            }

            $context = $route->getContext();

            if (0 !== strpos($path, $context->getStaticPath())) {
                continue;
            }

            if (preg_match($context->getRegexp(), $path, $matches)) {
                return new MatchContext(
                    self::MATCH,
                    $name,
                    $path,
                    $route->getHandler(),
                    $this->getMatchedParams($route, $matches)
                );
            }
        }

        return new MatchContext(self::NOMATCH, null, null, null);
    }

    /**
     * reduce
     *
     * @param RouteCollectionInterface $routes
     * @param RequestContext $context
     *
     * @return RouteCollectionInterface
     */
    private function reduce(RouteCollectionInterface $routes, RequestContext $context)
    {
        return $routes->findByMethod($context->getMethod())->findByScheme($context->getScheme());
    }

    /**
     * getMatchedParams
     *
     * @param RouteInterface $route
     * @param array $matches
     *
     * @return array
     */
    private function getMatchedParams(RouteInterface $route, array $matches)
    {
        $params = array_merge($route->getDefaults(), $matches);

        return array_intersect_key($params, array_flip($route->getContext()->getParameters()));
    }
}
