<?php

/*
 * This File is part of the Lucid\Mux\Cache\Storage package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Storage;

use InvalidArgumentException;
use Lucid\Mux\Cache\StorageInterface;
use Lucid\Mux\RouteCollectionInterface;

/**
 * @class Filesystem
 *
 * @package Lucid\Mux\Cache\Storage
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Filesystem implements StorageInterface
{
    use StorageTrait;

    /** @var string */
    private $path;

    /** @var string */
    private $prefix;

    /** @var string */
    private $exists;

    /**
     * Constructor.
     *
     * @param string $path
     * @param string $prefix
     */
    public function __construct($path, $prefix = self::DEFAULT_PREFIX)
    {
        $this->prefix = $prefix;
        $this->ensureExists($path);
        $this->path = $path;
        $this->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return $this->exists ? $this->getContent($this->getFilePath()) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function write(RouteCollectionInterface $routes)
    {
        $this->putContent($this->getFilePath(), $this->getCollection($routes));
        $this->exists = true;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($time)
    {
        return $this->getLastWriteTime() <= $time;
    }

    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return $this->exists = file_exists($this->getFilePath());
    }

    /**
     * {@inheritdoc}
     */
    public function getLastWriteTime()
    {
        if ($this->exists()) {
            return filemtime($this->getFilePath());
        }

        return time();
    }

    /**
     * getContent
     *
     * @param mixed $file
     *
     * @access private
     * @return mixed
     */
    private function getContent($file)
    {
        return unserialize(file_get_contents($file));
    }

    /**
     * putContent
     *
     * @param mixed $file
     * @param mixed $content
     *
     * @access private
     * @return boolean
     */
    private function putContent($file, $content)
    {
        $this->ensureExists(dirname($file));
        touch($file);

        return file_put_contents($file, serialize($content));
    }

    /**
     * ensureExists
     *
     * @param string $dir
     *
     * @return void
     */
    private function ensureExists($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $this->validatePath($dir);
    }

    /**
     * getFilePath
     *
     * @return string
     */
    private function getFilePath()
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->prefix . '.routes';
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function validatePath($path)
    {
        if (is_readable($path) && is_writable($path)) {
            return true;
        }

        throw new InvalidArgumentException(sprintf('Invalid path "%s".', $path));

    }
}
