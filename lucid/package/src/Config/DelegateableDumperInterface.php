<?php

/*
 * This File is part of the Lucid\Package\Config package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package\Config;

/**
 * @interface DelegateableDumperInterface
 *
 * @package Lucid\Package\Config
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface DelegateableDumperInterface
{
    /**
     * Returns a config dumper.
     *
     * @param mixed $format
     *
     * @return DumperInterface
     */
    public function getDumper($format);
}
