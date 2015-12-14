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
 * @class AbstractFileLoader
 *
 * @package Lucid\Config
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractFileLoader extends AbstractLoader
{
    /** @var LocatorInterface */
    private $locator;

    /** @var string */
    protected static $extension;

    /**
     * Constructor.
     *
     * @param LocatorInterface $locator
     */
    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inhertidoc}
     */
    final public function supports($resource)
    {
        return is_string($resource) && static::$extension ===  pathinfo(strtolower($resource), PATHINFO_EXTENSION);
    }

    /**
     * findResourceOrigin
     *
     * @access protected
     * @return string|array
     */
    protected function findResource($resource, $any = self::LOAD_ONE)
    {
        return $this->locator->locate($resource, $any);
    }
}
