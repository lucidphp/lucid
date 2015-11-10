<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template;

use Lucid\Template\Resource\FileResource;
use Lucid\Template\Resource\ResourceInterface;

/**
 * @class Identity
 * @see IdentityInterface
 *
 * @package Lucid\Template
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
     * Constructor.
     *
     * @param string $name
     * @param string $type
     * @param string $path
     */
    public function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
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
