<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource;

/**
 * @class FileResource
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FileResource extends AbstractResource
{
    /**
     * Constructor.
     *
     * @param mixed $file
     *
     * @return void
     */
    public function __construct($file)
    {
        $this->resource = $file;
    }
}
