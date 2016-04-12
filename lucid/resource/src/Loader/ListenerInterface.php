<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource\Loader;

use Lucid\Resource\ResourceInterface;

/**
 * @interface ListenerInterface
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ListenerInterface
{
    /**
     * Method to be called if a resources got loaded.
     *
     * @return void
     */
    public function onLoaded(ResourceInterface $resource);
}
