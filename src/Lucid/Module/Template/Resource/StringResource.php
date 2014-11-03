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
 * @class StringResource
 *
 * @package Lucid\Module\Template\Resource
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
        $this->content = $content;
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
