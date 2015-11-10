<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Request;

use RuntimeException;
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
     * Create a new instance of `Body` from the php input stream.
     *
     * @param boolean $multipart tell if the incomming request is multipart.
     * @throws RuntimeException if creating stream from $HTTP_RAW_POST_DATA
     * fails.
     *
     * @throws RuntimeException if multipart is requested but
     * `$HTTP_RAW_POST_DATA` is missing.
     *
     * @return StreamableInterface New instance of Body.
     */
    public static function createFromInput($multipart = false)
    {
        if (!$multipart) {
            return new static(fopen('php://input', 'rb'));
        }

        if (isset($HTTP_RAW_POST_DATA)) {
            return static::createFromString($HTTP_RAW_POST_DATA);
        }

        throw new RuntimeException('Cannot create body for multipart data.');
    }

    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException if it fails to create a in-memory
     * stream.
     *
     * @return self
     */
    public static function createFromString($content)
    {
        if (($stream = fopen('php://memory', 'rwb')) && false !== fwrite($stream, $content)) {
            return new static($stream, mb_strlen($content));
        }

        throw new RuntimeException('Cannot create body.');
    }
}
