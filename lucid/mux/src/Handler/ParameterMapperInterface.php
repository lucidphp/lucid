<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
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
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface ParameterMapperInterface
{
    /**
     * @param Reflector $handler
     * @param array $parameters
     * @return mixed
     */
    public function map(Reflector $handler, array $parameters);
}
