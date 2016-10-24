<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Cache package
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
use Lucid\Mux\Cache\CachedCollectionInterface;


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
    private $doesExist;

    /**
     * Filesystem constructor.
     *
     * @param string $path
     * @param string $prefix
     */
    public function __construct(string $path, string $prefix = self::DEFAULT_PREFIX)
    {
        $this->prefix = $prefix;
        $this->ensureExists($path);
        $this->path = $path;
        $this->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function read() : ?CachedCollectionInterface
    {
        return $this->doesExist ? $this->getContent($this->getFilePath()) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function write(RouteCollectionInterface $routes) : void
    {
        $this->doesExist =
            (bool)$this->putContent($this->getFilePath(), $this->getCollection($routes));
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(int $time) : bool
    {
        return $this->getLastWriteTime() <= $time;
    }

    /**
     * {@inheritdoc}
     */
    public function exists() : bool
    {
        return $this->doesExist = file_exists($this->getFilePath());
    }

    /**
     * {@inheritdoc}
     */
    public function getLastWriteTime() : int
    {
        if ($this->exists()) {
            return filemtime($this->getFilePath());
        }

        return time();
    }

    /**
     * getContent
     *
     * @param string $file
     *
     * @return CachedCollectionInterface
     */
    private function getContent($file) : CachedCollectionInterface
    {
        return unserialize(file_get_contents($file));
    }

    /**
     * putContent
     *
     * @param string $file
     * @param string $content
     *
     * @return int
     */
    private function putContent($file, $content) : int
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
    private function ensureExists($dir) : void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->validatePath($dir);
    }

    /**
     * getFilePath
     *
     * @return string
     */
    private function getFilePath() : string
    {
        return $this->path . DIRECTORY_SEPARATOR . $this->prefix . '.routes';
    }

    /**
     * @param string $path
     *
     * @throws \InvalidArgumentException
     * @return void
     */
    private function validatePath(string $path) : void
    {
        if (is_readable($path) && is_writable($path)) {
            return;
        }

        throw new InvalidArgumentException(sprintf('Invalid path "%s".', $path));
    }
}
