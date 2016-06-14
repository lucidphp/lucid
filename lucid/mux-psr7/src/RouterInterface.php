<?php

/*
 * This File is part of the Lucid\Mux\Psr7 package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Psr7;

use Psr\Http\Message\ServerRequestInterface;
use Lucid\Mux\Matcher\ContextInterface as MatchContextInterface;

/**
 * @interface RouterInterface
 *
 * @package Lucid\Mux\Psr7
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouterInterface
{
    /**
     * dispatch
     *
     * @param ServerRequestInterface $request
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request);
}
