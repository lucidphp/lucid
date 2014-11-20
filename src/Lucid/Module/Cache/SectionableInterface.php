<?php

/*
 * This File is part of the Lucid\Module\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Cache;

/**
 * @interface Sectionable
 *
 * @package Lucid\Module\Cache
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
