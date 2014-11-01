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
 * @class TemplateIdentity
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TemplateIdentity implements TemplateIdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public function identify($template)
    {
        if ($template instanceof TemplateInterface) {
            return $template;
        }

        $type = substr($template, -strrpos($template, '.'));

        return new Template($template, $type);
    }
}
