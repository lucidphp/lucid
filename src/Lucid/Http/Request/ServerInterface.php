<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Request;

use Lucid\Http\ParameterInterface;

/**
 * @interface ServerInterface
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ServerInterface extends ParameterInterface
{
    /**
     * getHeaders
     *
     * @return ParameterInterface
     */
    public function getHeaders();
}
