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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @interface CompleteRequestInterface
 *
 * @package Lucid\Http\Core
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RequestCompleteInterface
{
    /**
     * Terminates a request with a given response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function complete(ServerRequestInterface $request, ResponseInterface $response);
}
