<?php

/*
 * This File is part of the Lucid\Module\Filesystem\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Cache;

use Lucid\Module\Filesystem\Driver\DriverInterface;

/**
 * @class AbstractCache
 *
 * @package Lucid\Module\Filesystem\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractCache implements CacheInterace
{
    /**
     * driver
     *
     * @var mixed
     */
    protected $driver;

    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * {@inheritdoc}
     */
    public function getSeparator()
    {
        return $this->getDriver()->getSeparator();
    }
}
