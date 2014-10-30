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
 * @interface TypeMapCollectionInterface
 *
 * @package Lucid\Module\Routing\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface TypeMapCollectionInterface
{
    public function set(array $typeMappers);

    public function add(TypeMapperInterface $typeMapper);

    public function has($type);

    public function get($type);
}
