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

use Lucid\Module\Template\Resource\ResourceInterface;

/**
 * @class TemplateInterface
 *
 * @package Lucid\Module\Template
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
}
