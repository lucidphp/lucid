<?php

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
     * reduce
     *
     * @param RouteCollectionInterface $routes
     * @param RequestContext $context
     *
     * @return RouteCollectionInterface
     */
    private function filterByMethodAndScheme(RouteCollectionInterface $routes, Request $context)
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
    private function getMatchedVars(RouteInterface $route, array $matches)
    {
        $vars = $route->getContext()->getVars();
        $params = array_merge(
            $route->getDefaults(),
            array_map(
                [$this, 'getValue'],
                array_intersect_key(
                    $matches,
                    $t = array_combine($vars, array_pad([], count($vars), null))
                )
            )
        );

        return array_intersect_key($params, $t);
    }

    /**
     * getValue
     *
     * @param string $val
     *
     * @return mixed string|int|float
     */
    private function getValue(array $val)
    {
        if (is_numeric($val[0])) {
            return 0 + $val[0];
        }

        return $val[0];
    }
}
