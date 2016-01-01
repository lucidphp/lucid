<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer;

/**
 * @trait Stringable
 * @package Lucid\Writer
 * @version $Id$
 */
trait Stringable
{
    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->generate(GeneratorInterface::RV_STRING);
    }
}
