<?php

/*
 * This File is part of the Lucid\Module\Routing\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Handler;

/**
 * @interface HandlerParserInterface
 *
 * @package Lucid\Module\Routing\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface HandlerParserInterface
{
    /**
     * Parsers a route handler to an executable HandlerReflector.
     *
     * @param mixed $handler the route handler
     *
     * @return HandlerReflector
     */
    public function parse($handler);
}
