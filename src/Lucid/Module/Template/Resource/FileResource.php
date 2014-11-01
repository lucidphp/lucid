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
 * @class FileResource
 *
 * @package Lucid\Module\Template\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FileResource implements ResourceInterface
{
    /**
     * resource
     *
     * @var string
     */
    private $resource;

    /**
     * Constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->resource = $file;
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
    public function getContents()
    {
        return file_get_contents($this->content);
    }
}
