<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Matcher;

use Lucid\Mux\RouteInterface;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Request\ContextInterface as Request;
use Lucid\Mux\Cache\CachedCollectionInterface;
use Lucid\Mux\Matcher\Context as MatchContext;

/**
 * @class RequestMatcher
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class RequestMatcher implements RequestMatcherInterface
{
    use MatcherTrait;

    /**
     * Matches a
     * @param \Lucid\Mux\Request\ContextInterface $request
     * @param \Lucid\Mux\RouteCollectionInterface $routes
     *
     * @return \Lucid\Mux\Matcher\Context
     */
    public function matchRequest(Request $request, RouteCollectionInterface $routes) : MatchContext
    {
        $path   = $request->getPath();
        $filtered = $this->filterByMethodAndScheme($routes, $request);

        if ($filtered instanceof CachedCollectionInterface && 0 !== count($r = $routes->findByStaticPath($path))) {
            $filtered = $r;
        }

        $noMatch = array_diff_key($routes->all(), $filtered->all());

        foreach ($filtered->all() as $name => $route) {
            $rctx = $route->getContext();

            // does it match host?
            if (!$this->matchHost($rctx, $request, $route->getHost())) {
                $noMatch[$name] = $route;
                continue;
            }

            // does it match static path?
            if (0 !== strpos($path, $rctx->getStaticPath())) {
                continue;
            }

            // does it match pattern described on the route?
            if ((bool)preg_match_all($rctx->getRegex(), $path, $matches)) {
                $vars    = $this->getMatchedVars($route, $matches);
                $handler = $route->getHandler();

                return new MatchContext(self::MATCH, $name, $request, $handler, $vars);
            }

            $noMatch[$name] = $route;
        }

        return new MatchContext($this->getMatchFailureReason($noMatch, $request), null, $request, null);
    }
}
