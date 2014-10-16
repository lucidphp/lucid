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
 * @class ParameterMapperInterface
 *
 * @package Lucid\Module\Routing\Handler
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
    public function map(callable $handler, array $parameters);
}
