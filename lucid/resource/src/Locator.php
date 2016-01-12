<?php

/*
 * This File is part of the Lucid\Resource package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Resource;

use InvalidArgumentException;
use Lucid\Resource\Loader\LoaderInterface;

/**
 * @class Locator
 * @see LocatorInterface
 *
 * @package Lucid\Resource
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Locator implements LocatorInterface
{
    /** @var array */
    private $paths;

    /** @var string */
    private $cwd;

    /**
     * Constructor.
     *
     * @param array  $paths
     * @param string $cwd
     */
    public function __construct(array $paths = [], $cwd = null)
    {
        $this->setPaths($paths);
        $this->setRootPath($cwd ?: getcwd());
    }

    /**
     * {@inheritdoc}
     */
    public function locate($file, $collection = LoaderInterface::LOAD_ONE)
    {
        if (!is_dir($this->cwd)) {
            throw new InvalidArgumentException(sprintf('%s is not a directory.', $this->cwd));
        }

        $resources = [];

        foreach ($this->paths as $path) {
            if (1 === $this->locateResource($path, $file, $collection, $resources)) {
                break;
            }
        }

        return new Collection($resources);
    }

    /**
     * {@inheritdoc}
     */
    public function setRootPath($root)
    {
        $this->cwd = $root;
    }

    /**
     * {@inheritdoc}
     */
    public function addPath($path)
    {
        if (in_array($path, $this->paths)) {
            return;
        }

        $this->paths[] = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function addPaths(array $paths)
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setPaths(array $paths)
    {
        $this->paths = [];

        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * locateResource
     *
     * @param string  $path
     * @param string  $file
     * @param bool    $collect
     * @param array   $collection
     *
     * @return int
     */
    protected function locateResource($path, $file, $collect = LoaderInterface::LOAD_ONE, array &$collection = [])
    {
        if (false === ($dir = $this->expandPath($path)) || !is_dir($dir)) {
            return;
        }

        if (!file_exists($resource = $dir . DIRECTORY_SEPARATOR . $file)) {
            return;
        }

        $collection[] = new FileResource($resource);

        if (LoaderInterface::LOAD_ONE === $collect) {
            return 1;
        }

        return 0;
    }

    /**
     * Expands a path to full path.
     *
     * @param string $path
     *
     * @return string or false if path is invalid.
     */
    private function expandPath($path)
    {
        if (0 === strspn($path, '\\/', 0, 1) || null === parse_url($path, PHP_URL_PATH)) {
            $path = $this->cwd.DIRECTORY_SEPARATOR.$path;
        }

        return realpath($path);
    }
}
