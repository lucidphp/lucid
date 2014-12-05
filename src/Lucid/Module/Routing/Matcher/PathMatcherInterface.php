<?php

/*
 * This File is part of the Lucid\Module\Routing\Matcher package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Matcher;

/**
 * @interface PathMatcherInterface
 *
 * @package Lucid\Module\Routing\Matcher
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface PathMatcherInterface
{
    /**
     * match
     *
     * @param mixed $path
     *
     * @return void
     */
    public function match($path);
}
