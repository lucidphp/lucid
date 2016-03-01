<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Matcher;

use RuntimeException;
use Lucid\Mux\Matcher\MatcherTrait;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Matcher\RequestMatcherInterface;
use Lucid\Mux\Matcher\Context as Match;
use Lucid\Mux\Request\ContextInterface as Request;
use Lucid\Mux\Cache\CachedCollectionInterface as CachedCollection;

/**
 * @class FastMatcher
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FastMatcher implements RequestMatcherInterface
{
    use MatcherTrait;

    /** @var array */
    protected $map;

    /**
     * Constructor.
     *
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request, RouteCollectionInterface $routes)
    {
        $routes = $this->preFilterRoutes($request, $routes);
        $method = strtolower($request->getMethod());

        if (!isset($this->map[$method]) ||
            !(bool)preg_match_all($this->map[$method]['regex'], $request->getPath(), $matches, PREG_SET_ORDER)) {
            return $this->noMatch();
        }

        return $this->postFilterRoutes($request, $routes, $this->map[$method], $matches);
    }

    /**
     * preFilterRoutes
     *
     * @param RouteCollectionInterface $routes
     *
     * @return RouteCollectionInterface
     */
    protected function preFilterRoutes(Request $request, RouteCollectionInterface $routes)
    {
        if ($routes instanceof CachedCollection && $sRoutes = $routes->findByStaticPath($request->getPath())) {
            $routes = $sRoutes;
        }

        return $routes->findByMethod($request->getMethod());
    }

    /**
     * postFilterRoutes
     *
     * @param Request $request
     * @param RouteCollectionInterface $routes
     * @param array $matches
     *
     * @return Lucid\Mux\Matcher\ContextInterface
     */
    protected function postFilterRoutes(Request $request, RouteCollectionInterface $routes, array $map, array $matches)
    {
        try {
            list ($name, $route, $vars) = $this->reverseMapRoute($routes, $matches, $map['map']);
        } catch (RuntimeException $e) {
            return $this->noMatch();
        }

        if (null !== $route->getHost() &&
            !(bool)preg_match_all($route->getContext()->getHostRegex(), $request->getHost())) {
            var_dump('FUCK IT', $route->getContext()->getHostRegex());
            return $this->noMatch();
        }

        return new Match(self::MATCH, $name, $request->getPath(), $route->getHandler(), $vars);
    }

    /**
     * noMatch
     *
     * @return \Lucid\Mux\Matcher\ContextInterface
     */
    protected function noMatch($name = null)
    {
        return new Match(self::NOMATCH, $name, null, null);
    }

    /**
     * reverseMapRoute
     *
     * @param RouteCollectionInterface $routes
     * @param array $matches
     * @param array $map
     *
     * @return array [Lucid\Mux\RouteInterface, array]
     */
    private function reverseMapRoute(RouteCollectionInterface $routes, array $matches, array $map = [])
    {
        foreach ($matches = array_filter($matches[0]) as $key => $subject) {
            if (is_int($key)) {
                continue;
            }

            if (!isset($map[$key])) {
                continue;
            }

            list ($index, $name, $prefix) = $map[$key];
            $route = $routes->get($name);
            $args  = [];

            foreach ($route->getContext()->getVars() as $var) {
                $args[$var] = $matches[$prefix.$var];
            }

            return [$name, $route, $args];
        }

        throw new RuntimeException('No match found.');
    }
}
