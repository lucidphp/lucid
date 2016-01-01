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
 * @class GeneratorInterface
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface GeneratorInterface
{
    /** @var bool */
    const RV_STRING = false;

    /** @var bool */
    const RV_WRITER = true;

    /**
     * generate
     *
     * @param bool $raw
     *
     * @return string|WriterInterface
     */
    public function generate($raw = self::RV_STRING);

    public function __toString();
}
