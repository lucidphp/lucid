<?php

/*
 * This File is part of the Lucid\Package\Dumper package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package\Config;

/**
 * @interface DumperInterface
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface DumperInterface
{
    /**
     * supports
     *
     * @param mixed $format
     *
     * @return bool
     */
    public function supports($format);

    /**
     * getFilename
     *
     * @return string
     */
    public function getFilename();

    /**
     * dump
     *
     * @param string $name
     * @param array $contents
     *
     * @return string
     */
    public function dump($name, array $contents = [], $format = null);
}
