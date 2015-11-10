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
 * @class Stringable
 * @package Lucid\Writer
 * @version $Id$
 */
trait Stringable
{
    public function __toString()
    {
        if ($this instanceof GeneratorInterface) {
            return $this->generate(GeneratorInterface::RV_STRING);
        }

        if ($this instanceof Writer) {
            return $this->dump();
        }
    }
}
