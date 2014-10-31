<?php

/*
 * This File is part of the Lucid\Module\Routing\Matcher package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Matcher;

use Lucid\Module\Routing\RouteInterface;
use Lucid\Module\Routing\RouteCollectionInterface;
use Lucid\Module\Routing\Http\RequestContextInterface;
use Lucid\Module\Routing\Cache\CachedCollectionInterface;

/**
 * @class RequestMatcher
 *
 * @package Lucid\Module\Routing\Matcher
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
    public function matchRequest(RequestContextInterface $context, RouteCollectionInterface $routes)
    {
        // basic filter:
        $routes = $routes->findByMethod($context->getMethod());
        $routes = $routes->findByScheme($context->getScheme());

        $path = $context->getPath();

        if ($routes instanceof CachedCollectionInterface && 0 !== count($r = $route->findByStaticPath($path))) {
            $routes = $r;
        }

        foreach ($routes->all() as $name => $route) {
            if (
                null !== ($host = $route->getHost())
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
