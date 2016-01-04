<?php

/*
 * This File is part of the Lucid\Package package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package;

/**
 * @interface ProviderInterface
 *
 * @package Lucid\Package
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ProviderInterface
{
    /**
     * Define an array of packages that are required by this package.
     *
     * @return array `string[]`.
     */
    public function requires();

    /**
     * Returns the package namespace.
     *
     * @return string
     */
    public function getNamespace();

    /**
     * Returns the file path to the package.
     *
     * @return string
     */
    public function getPath();

    /**
     * Returns the package alias.
     *
     * @return string
     */
    public function getAlias();

    /**
     * Returns the package postfix.
     *
     * @return string
     */
    public function getPostFix();
}
