<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\File;

use LogicException;

/**
 * @class FileInfo
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FileInfo
{
    /** @var string */
    public $name;

    /** @var string */
    public $tmpName;

    /** @var int */
    public $size;

    /** @var string */
    public $type;

    /** @var int */
    public $error;

    /**
     * Constructor.
     *
     * @param string $name
     * @param int $size
     * @param string $type
     * @param int $error
     */
    public function __construct($name, $size, $type, $tmpName, $error = null)
    {
        $this->name    = $name;
        $this->tmpName = $tmpName;
        $this->size    = $size;
        $this->type    = $type;
        $this->error   = $error;
    }

    /**
     * Restricts Setter for public properties.
     *
     * @param string $key
     * @param mixed $value
     * @throws LogicException if a undefined properties is about to be set.
     *
     * @return void
     */
    public function __set($key, $value)
    {
        throw new LogicException(sprintf('Can\'t set undefined property "%s".', $key));
    }
}
