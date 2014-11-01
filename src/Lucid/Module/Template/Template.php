<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template;

/**
 * @class Template
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Template implements TemplateInterface
{
    protected $name;
    protected $type;
    protected $path;

    public function __construct($name, $type, $path = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->path = $path;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path ?: $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

}
