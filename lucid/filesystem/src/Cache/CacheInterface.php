<?php

/*
 * This File is part of the Lucid\Filesystem\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Filesystem\Cache;

use Lucid\Filesystem\Driver\DriverInterface;

/**
 * @class CacheInterface
 *
 * @package Lucid\Filesystem\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface CacheInterface extends CacheableInterface
{
    public function init(DriverInterface $driver);
}
