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
 * @class StringResource
 *
 * @package Lucid\Template\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class StringResource extends AbstractResource
{
    private $content;

    /**
     * Constructor.
     *
     * @param string $string
     */
    public function __construct($string)
    {
        $this->content = $string;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        return $this->content;
    }
}
