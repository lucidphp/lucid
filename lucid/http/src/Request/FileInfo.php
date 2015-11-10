<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Request;

/**
 * @class
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FileInfo
{
    /**
     * name
     *
     * @var string
     */
    public $name;

    /**
     * tmpName
     *
     * @var string
     */
    public $tmpName;

    /**
     * size
     *
     * @var int
     */
    public $size;

    /**
     * type
     *
     * @var string
     */
    public $type;

    /**
     * error
     *
     * @var int
     */
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
        $this->name = $name;
        $this->tmpName = $tmpName;
        $this->size = $size;
        $this->type = $type;
        $this->error = $error;
    }

    public function __set($key, $value)
    {
        throw new \LogicException('dont\' touch my stuff');
    }
}
