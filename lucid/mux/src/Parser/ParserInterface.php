<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Parser;

use Lucid\Mux\RouteInterface;
use Lucid\Mux\RouteContextInterface as ContextInterface;

/**
 * @interface ParserInterface
 *
 * @package Lucid\Mux\Parser
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ParserInterface
{
    /** @var string */
    const SEPARATORS   = '/.;:-_~+*=|';

    /** @var string */
    const EXP_DELIM    = '#';

    /**
     * parse
     *
     * @param RouteInterface $route
     *
     * @return ContextInterface
     */
    public static function parse(RouteInterface $route);
}
