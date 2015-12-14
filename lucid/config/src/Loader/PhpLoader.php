<?php

/*
 * This File is part of the Lucid\Config package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Config\Loader;

use Lucid\Resource\Loader\AbstractLoader;
use Lucid\Resource\Locator\LocatorInterface;

/**
 * @class PhpLoader
 *
 * @package Lucid\Config
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpLoader extends AbstractLoader
{
    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    public function load($resource)
    {
    }

    public function import($resource)
    {
    }

    public function supports($resource)
    {
    }

    protected function doLoad($resource)
    {
    }
}
