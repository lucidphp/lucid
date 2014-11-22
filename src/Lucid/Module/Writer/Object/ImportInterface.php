<?php

/*
 * This File is part of the Lucid\Module\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Writer\Object;

/**
 * @interface ImportInterface
 *
 * @package Lucid\Module\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ImportInterface
{
    public function setResolver(ImportResolver $resolver);
}
