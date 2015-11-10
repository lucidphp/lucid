<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Cache;

/**
 * @interface Sectionable
 *
 * @package Lucid\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SectionableInterface
{
    /**
     * Creates a cache section.
     *
     * @param string $section
     *
     * @return CacheInterface
     */
    public function section($section);
}
