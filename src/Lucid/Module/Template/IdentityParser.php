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
 * @class IdentityParser
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class IdentityParser
{
    /**
     * {@inheritdoc}
     */
    public function identify($template)
    {
        if ($template instanceof IdentityInterface) {
            return $template;
        }

        $type = substr((string)$template, 1+strrpos((string)$template, '.'));

        return new Identity((string)$template, $type);
    }
}
