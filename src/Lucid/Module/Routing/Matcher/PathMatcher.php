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

use Lucid\Module\Routing\RouteCollectionInterface;

/**
 * @class PathMatcher
 *
 * @package Lucid\Module\Routing\Matcher
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PathMatcher implements PathMatcherInterface, RequestMatcherInterface
{
    private $regexp;

    public function __construct($regexp)
    {
        $this->regexp = $regexp;
    }

    /**
     * match
     *
     * @param mixed $path
     * @param RouteCollectionInterface $routes
     *
     * @return void
     */
    public function match($path, RouteCollectionInterface $routes)
    {
        if (preg_match($this->getRegexp(), $path = rawurldecode($path))) {
            if ($context = $this->findRoute($routes, $path)) {
                return $context;
            }
        }

        return new MatchContext(MatchContextInterface::NO_MATCH, $path);
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(RequestContextInterface $req, RouteCollectionInterface $routes)
    {
        return $this->match($req->getPath(), $routes);
    }

    /**
     * findRoute
     *
     * @param mixed $routes
     * @param mixed $path
     *
     * @return void
     */
    protected function findRoute($routes, $path)
    {
        foreach ($routes->all() as $name => $route) {
            if (0 !== strpos($path, $spath = $route->getContext()->getStaticPath())) {
                continue;
            }

            if (preg_match($route->getContext()->getRegexp(), $path, $matches)) {
                return new MatchContext(
                    MatchContextInterface::MATCH,
                    $name,
                    $path,
                    $route->getHandler(),
                    $matches
                );
            }
        }
    }

    /**
     * getRegexp
     *
     * @return string
     */
    protected function getRegexp()
    {
        return sprintf('~%s~', $this->regexp);
    }
}
