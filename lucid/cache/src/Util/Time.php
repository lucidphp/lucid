<?php

/*
 * This File is part of the Lucid\Cache\Util package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache\Util;

/**
 * @class Time
 *
 * @package Lucid\Cache\Util
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Time
{

    public function validateExpires($expires)
    {
    }

    public function expiryToUnixTimestamp($expiry)
    {
        if (is_string($expiry = self::validateExpires($expiry))) {
            return strtotime($expiry);
        }

        return CacheInterface::PERSIST === $expiry ? $this->getPersistLevel() : time()  + ($expiry * 60);
    }
}
