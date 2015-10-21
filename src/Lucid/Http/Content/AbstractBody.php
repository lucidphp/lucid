<?php

/*
 * This File is part of the Lucid\Http\Content package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Content;

use Lucid\Http\Traits\StreamedBody;
use Psr\Http\Message\StreamInterface;

/**
 * @class AbstractBody
 *
 * @package Lucid\Http\Content
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractBody implements StreamInterface
{
    use StreamedBody;

    /**
     * Constructor.
     *
     * @param resrouce $input php input stream
     */
    /**
     * Constructor.
     *
     * @param string $input
     * @param int $size
     */
    final public function __construct($input, $size = null)
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
     * Create a new instance of `AbstractBody` implementation from a given string.
     *
     * @param string $content
     *
     * @return StreamInterface New instance of Body.
     */
    abstract public static function createFromString($content);
}
