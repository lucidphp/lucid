<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http;

use Psr\Http\Message\IncomingRequestInterface;

/**
 * @interface RequestInterface
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RequestInterface extends IncomingRequestInterface
{
    const T_MAIN = 'main';
    const T_SUB = 'sub';

    /**
     * getRequestUri
     *
     * @return void
     */
    public function getRequestUri();

    public function getPathInfo();

    /**
     * getHost
     *
     * @return void
     */
    public function getHost();

    /**
     * getPort
     *
     * @return void
     */
    public function getPort();
}
