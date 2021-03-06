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

use Lucid\Template\Resource\ResourceInterface;

/**
 * @class TemplateInterface
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface IdentityInterface
{
    /**
     * Get the template name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the tempalte type
     *
     * @return string
     */
    public function getType();

    /**
     * __toString
     *
     * @return string
     */
    public function __toString();
}
