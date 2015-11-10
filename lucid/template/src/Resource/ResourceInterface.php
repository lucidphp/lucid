<?php

/*
 * This File is part of the Lucid\Template\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Resource;

/**
 * @class ResourceInterface
 *
 * @package Lucid\Template\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResourceInterface
{
    public function getResource();

    public function getContents();

    /**
     * getHash
     *
     * @return void
     */
    public function getHash();
}
