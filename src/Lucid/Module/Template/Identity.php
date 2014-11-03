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

use Lucid\Module\Template\Resource\FileResource;
use Lucid\Module\Template\Resource\ResourceInterface;

/**
 * @class Identity
 * @see IdentityInterface
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Identity implements IdentityInterface
{
    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * type
     *
     * @var string
     */
    protected $type;

    /**
     * path
     *
     * @var string
     */
    protected $resource;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $type
     * @param string $path
     */
    public function __construct($name, $type, ResourceInterface $resource = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function setResource(ResourceInterface $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function isFile()
    {
        return $this->resource ? $this->resource instanceof FileResource : is_file($this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->name;
    }
}
