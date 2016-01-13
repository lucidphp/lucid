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

/**
 * @class Parser
 *
 * @package Lucid\Mux\Parser
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Parser implements ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse(RouteInterface $route)
    {
        return Standard::parse($route);
    }
}
