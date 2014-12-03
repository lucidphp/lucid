<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session;

use Lucid\Module\Http\ParameterInterface;

/**
 * @interface SessionInterface
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SessionInterface extends ParameterInterface
{
    /**
     * Starts a session.
     *
     * @return void
     */
    public function start();

    /**
     * Refresh a session
     *
     * @return void
     */
    public function refresh();
}
