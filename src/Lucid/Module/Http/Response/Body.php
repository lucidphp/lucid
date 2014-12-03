<?php

/*
 * This File is part of the Lucid\Module\Http\Response package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Response;

use Psr\Http\Message\StreamableInterface;
use Lucid\Module\Http\Traits\StreamedBody;

/**
 * @class Body
 *
 * @package Lucid\Module\Http\Response
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
     * createFromString
     *
     * @param mixed $content
     *
     * @return void
     */
    public static function createFromString($content)
    {
        $stream = fopen('php://memory', 'rwb');
        fputs($stream, $c = (string)$content, mb_strlen($c));

        return new static($stream);
    }
}
