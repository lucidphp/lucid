<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI;

/**
 * @class ScopeInterface
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Scope
{
    /** @var string */
    const SINGLETON = 'singleton';

    /** @var string */
    const PROTOTYPE = 'prototype';

    /**
     * Constructor
     *
     * @param string $type
     * @param Scope $parent
     */
    public function __construct($type = self::SINGLETON, Scope $parent = null)
    {
        $this->type = $type;
        $this->parent = $parent;
    }

    /**
     * getParent
     *
     * @return Scope|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }
}
