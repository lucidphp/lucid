<?php

/*
 * This File is part of the Lucid\DI\Parameter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Parameter;

/**
 * @class Parameter
 *
 * @package Lucid\DI\Parameter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Value
{
    /** @var string */
    private $value;

    /**
     * Constructor.
     *
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = (string)$value;
    }

    /**
     * String representation of Vlaue.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
