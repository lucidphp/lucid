<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Handler;

/**
 * @class ParameterMapperInterface
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ParameterMapperInterface
{
    /**
     * map
     *
     * @param callable $handler
     * @param array $parameters
     *
     * @return array
     */
    public function map(Reflector $handler, array $parameters);
}
