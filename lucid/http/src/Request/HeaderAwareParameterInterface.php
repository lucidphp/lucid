<?php

/*
 * This File is part of the Lucid\Http\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Request;

use Lucid\Http\ParameterInterface;

/**
 * @interface ParametersServerInterface
 *
 * @package Lucid\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface HeaderAwareParameterInterface extends ParameterInterface
{
    /**
     * Get all headers as key => value pairs.
     *
     * @return array
     */
    public function getHeaders();
}
