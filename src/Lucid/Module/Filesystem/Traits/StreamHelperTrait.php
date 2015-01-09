<?php

/*
 * This File is part of the Lucid\Module\Filesystem\Traits package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Traits;

/**
 * @trait StreamHelperTrait
 *
 * @package Lucid\Module\Filesystem\Traits
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait StreamHelperTrait
{
    /**
     * getStreamSize
     *
     * @param resource $stream
     *
     * @return int
     */
    public function getStreamSize($stream)
    {
        $meta = stream_get_meta_data($stream);

        if ($meta['eof']) {
            return 0;
        }

        if (0 !== $meta['unread_bytes']) {
            return $meta['unread_bytes'];
        }

        $pos = ftell($stream);
        $bytes = mb_strlen(stream_get_contents($stream));

        if (0 === $pos) {
            rewind($stream);
        } else {
            // reset r/w pointer position
            fseek($stream, $pos);
        }

        return $bytes;
    }
}
