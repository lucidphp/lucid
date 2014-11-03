<?php

/*
 * This File is part of the Lucid\Module\Template\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Resource;

/**
 * @class AbstractResource
 *
 * @package Lucid\Module\Template\Resource
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
