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

/**
 * @interface TemplateIdentityInterface
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface IdentityParserInterface
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
