<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Object;

/**
 * @interface ImportInterface
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ImportInterface
{
    /**
     * Sets the ImportResolver instance.
     *
     * @param ImportResolver $resolver
     *
     * @return void
     */
    public function setResolver(ImportResolver $resolver);
}
