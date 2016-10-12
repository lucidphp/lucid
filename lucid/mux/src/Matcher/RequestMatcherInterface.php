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

use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Matcher\Context as MatchContext;
use Lucid\Mux\Request\ContextInterface as Request;

/**
 * RequestMatcherInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RequestMatcherInterface
{
    /** @var int */
    const MATCH = 1;

    /** @var int */
    const NOMATCH = -1;

    /** @var int */
    const NOMATCH_METHOD = -2;

    /** @var int */
    const NOMATCH_HOST = -3;

    /** @var int */
    const NOMATCH_SCHEME = -4;

    /**
     * Matches an request context against a route collection.
     *
     * @param Request $context
     * @param RouteCollectionInterface $routes
     *
     * @return MatchContext
     */
    public function matchRequest(Request $context, RouteCollectionInterface $routes) : MatchContext;
}
