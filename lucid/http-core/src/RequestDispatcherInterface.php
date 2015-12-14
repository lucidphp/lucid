<?php

/*
 * This File is part of the Lucid\Http\Core package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Core;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @interface DispatcherInterface
 *
 * @package Lucid\Http\Core
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RequestDispatcherInterface
{
    /** @var int */
    const T_MAIN = 0;

    /** @var int */
    const T_SUB  = -1;

    /**
     * Dispatches a server request.
     *
     * @param ServerRequestInterface $request
     * @param int $type
     * @param bool $catchExceptions
     *
     * @return Psr\Http\Message\ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request, $type = self::T_MAIN, $catchExceptions = true);
}
