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
 * @interface TemplateIdentityInterface
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface TemplateIdentityInterface
{
    /**
     * identify
     *
     * @param mixed $template
     *
     * @return TemplateInterface
     */
    public function identify($template);
}
