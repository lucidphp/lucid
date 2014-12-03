<?php

/*
 * This File is part of the Lucid\Module\Http\Traits package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Traits;

use RuntimeException;
use InvalidArgumentException;

/**
 * @trait StreamedBody
 *
 * @package Lucid\Module\Http\Traits
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait StreamedBody
{
    /**
     * resource
     *
     * @var resource
     */
    private $resource;

    /**
     * size
     *
     * @var int
     */
    private $size;

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (null !== $this->resource && 'stream' === get_resource_type($this->resource)) {
            fclose($this->resource);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        return ftell($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return fseek($this->resource, $offset, $whence);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function write($string)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        return fread($this->resource, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        return stream_get_contents($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        $meta = stream_get_meta_data($this->resource);

        return null === $key ? $meta : (isset($meta[$key]) ? $meta[$key] : null);
    }

    /**
     * setResource
     *
     * @param resource $resource
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private function setResource($resource)
    {
        if (!is_resource($resource) || 'stream' !== get_resource_type($resource)) {
            throw new InvalidArgumentException();
        }

        $this->resource = $resource;
    }
}
