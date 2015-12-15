<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Resource;

/**
 * @class AbstractResource
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractResource implements ResourceInterface
{
    /**
     * hash
     *
     * @var string
     */
    protected $hash;

    public function getHash()
    {
        return $this->hash ?: $this->hash = hash('sha256', serialize($this));
    }
}
