<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Response;

use Lucid\Http\Content\AbstractBody;

/**
 * @class Body
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Body extends AbstractBody
{
    /**
     * {@inheritdoc}
     *
     * @return self
     */
    public static function createFromString($content)
    {
        $stream = fopen('php://memory', 'rwb');
        fputs($stream, $c = (string)$content, mb_strlen($c));

        return new static($stream);
    }
}
