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
use Lucid\Mux\RouteContextInterface as RouteContext;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Request\ContextInterface as Request;

/**
 * @trait MatcherTrait
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait MatcherTrait
{
    /**
     * Filters routes by schema and returns the result set as a new collection.
     *
     * @param RouteCollectionInterface $routes
     * @param Request $context
     *
     * @return RouteCollectionInterface
     */
    private function filterByMethodAndScheme(
        RouteCollectionInterface $routes,
        Request $context
    ) : RouteCollectionInterface {
        return $routes->findByMethod($context->getMethod())->findByScheme($context->getScheme());
    }

    /**
     * Determine the reason of a failed matching attempt.
     *
     * @param array $noMatch
     * @param Request $request
     *
     * @return int
     */
    private function getMatchFailureReason(array $noMatch, Request $request) : int
    {
        $path = $request->getPath();

        /** @var RouteInterface[] $reduce */
        $reduce = array_filter($noMatch, function (RouteInterface $route) use ($path) {
            return (bool)preg_match_all($route->getContext()->getRegex(), $path);
        });

        foreach ($reduce as $name => $route) {
            if (!$this->matchHost($route->getContext(), $request, $route->getHost())) {
                return RequestMatcherInterface::NOMATCH_HOST;
            }

            if (!$route->hasScheme($request->getScheme())) {
                return RequestMatcherInterface::NOMATCH_SCHEME;
            }

            if (!$route->hasMethod($request->getMethod())) {
                return RequestMatcherInterface::NOMATCH_METHOD;
            }
        }

        return RequestMatcherInterface::NOMATCH;
    }

    /**
     * @param \Lucid\Mux\RouteContextInterface $ctx
     * @param \Lucid\Mux\Request\ContextInterface $request
     * @param null $host
     *
     * @return bool
     */
    private function matchHost(RouteContext $ctx, Request $request, $host = null) : bool
    {
        if (null === $host) {
            return true;
        }

        return (bool)preg_match_all($ctx->getHostRegex(), $request->getHost());
    }

    /**
     * getMatchedParams
     *
     * @param RouteInterface $route
     * @param array $matches
     *
     * @return array
     */
    private function getMatchedVars(RouteInterface $route, array $matches) : array
    {
        $vars = $route->getContext()->getVars();
        $params = array_merge(
            $route->getDefaults(),
            array_filter(array_map(
                [$this, 'getValue'],
                array_intersect_key(
                    $matches,
                    $t = array_combine($vars, array_pad([], count($vars), null))
                )
            ))
        );

        return array_intersect_key($params, $t);
    }

    /**
     * @param array $val
     *
     * @return int|string
     */
    private function getValue(array $val)
    {
        $value = urldecode($val[0]);

        if (is_numeric($value)) {
            return 0 + $value;
        }

        return $value;
    }
}
