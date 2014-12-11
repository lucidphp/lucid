<?php

/*
 * This File is part of the Lucid\Module\Http\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Request;

use Psr\Http\Message\StreamableInterface;
use Lucid\Module\Http\Traits\StreamedBody;

/**
 * @class Body
 *
 * @package Lucid\Module\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Body implements StreamableInterface
{
    use StreamedBody;

    /**
     * Constructor.
     *
     * @param resrouce $input php input stream
     */
    public function __construct($input, $size = null)
    {
        $this->setResource($input);
        $this->size = $size ? (int)$size : null;
    }

    /**
     * Closes the resource handle if still open.
     *
     * @see Body::close()
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Create a new instance of `Body` from the php input stream.
     *
     * @param boolean $multipart tell if the incomming request is multipart.
     * @throws RuntimeException if creating stream from $HTTP_RAW_POST_DATA
     * fails.
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

        throw new RuntimeException('Reason');
    }

    /**
     * Create a new instance of `Body` from a given string.
     *
     * @param string $content
     *
     * @return StreamableInterface New instance of Body.
     */
    public static function createFromString($content)
    {
        if ($stream = fopen('php://memory', 'rwb') && fwrite($stream, $content)) {
            return new static($stream, mb_strlen($content));
        }

        throw new RuntimeException('Reason');
    }
}
