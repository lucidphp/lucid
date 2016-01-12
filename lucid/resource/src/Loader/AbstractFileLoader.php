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

use Lucid\Resource\LocatorInterface;
use Lucid\Resource\Loader\AbstractLoader;

/**
 * @class AbstractFileLoader
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractFileLoader extends AbstractLoader
{
    /** @var LocatorInterface */
    private $locator;

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
        return is_string($resource)
            && in_array(pathinfo(strtolower($resource), PATHINFO_EXTENSION), $this->getExtensions());
    }

    /**
     * findResourceOrigin
     *
     * @access protected
     * @return CollectionInterface
     */
    final protected function findResource($resource, $any = self::LOAD_ONE)
    {
        return $this->locator->locate($resource, $any);
    }

    /**
     * Returns the supported extensions
     *
     * @return array
     */
    abstract protected function getExtensions();
}
