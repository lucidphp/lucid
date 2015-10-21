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
 * @class IdentityParser
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class IdentityParser implements IdentityParserInterface
{
    /**
     * pool
     *
     * @var array
     */
    private $pool = [];

    /**
     * {@inheritdoc}
     */
    public function identify($template)
    {
        if ($template instanceof IdentityInterface) {
            return $template;
        }

        $name = (string)$template;

        if (!isset($this->pool[$name])) {

            $type = null;

            if (false !== ($pos = strrpos($name, '.'))) {
                $type = substr($name, 1 + $pos);
            }

            $this->pool[$name] = new Identity($name, $type);
        }

        return $this->pool[$name];
    }
}
