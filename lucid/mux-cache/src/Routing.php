<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache;

use Lucid\Mux\Routes;
use Lucid\Resource\CollectionInterface;
use Lucid\Resource\Loader\LoaderInterface;
use Lucid\Resource\Collection as Resources;

/**
 * @class Routing
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Routing implements LoaderListener
{
    /** @var StorageInterface */
    private $storage;

    /** @var CollectionInterface */
    private $resources;

    /** @var LoaderInterface */
    private $loader;

    /** @var string */
    private $current;

    /** @var bool */
    private $debug;

    /** @var array */
    private $meta;

    /** @var array */
    private $metaCache;

    /**
     * Constructor
     *
     * @param string $res
     * @param Store $storage
     * @param Loader $loader
     * @param bool $debug
     */
    public function __construct($res, $manifest, RouteCacheInterface $cache, LoaderInterface $loader, $debug = false)
    {
        $this->debug     = $debug;
        $this->loader    = $loader;
        $this->storage   = $cache;
        $this->manifest  = $manifest;

        $this->meta      = [];
        $this->metaCache = [];

        $this->setResources($res);
        $this->loader->addListener($this);
    }

    /**
     * Checks if the cache is valid.
     *
     * @return bool
     */
    public function isValid()
    {
        if ($this->storage->exists() && $this->resources->isValid($this->storage->getLastWriteTime())) {
            return $this->isDebugging() ? $this->validateManifest() : true;
        }

        return false;
    }

    /**
     * Loads the routes into a collection.
     *
     * Loads the resources from cache if the cache is still valid, or re-loads
     * and re-creates the cache if neccessary.
     *
     * @return RouteCollectionInterface
     */
    public function load()
    {
        if (!$this->isValid()) {
            $this->doLoadResources();
        }

        return $this->storage->read();
    }

    /**
     * Loading callback.
     *
     * Collects files that've been included during the loading process of the
     * main resource files.
     *
     * @param string $resource
     *
     * @return void
     */
    public function onLoaded($resource)
    {
        if (!$this->isDebugging() || $this->current === $resource) {
            return;
        }

        $this->ensureCollector($this->current);
        $this->meta[$this->current]->addFileResource($resource);
    }

    /**
     * Check if in debug mode.
     *
     * @return bool
     */
    private function isDebugging()
    {
        return (bool)$this->debug;
    }

    /**
     * Set the main resource files.
     *
     * @param string|array|CollectorInterface $resources
     *
     * @return void
     */
    private function setResources($resources)
    {
        if (!$resources instanceof CollectionInterface) {
            $files = (array)$resources;
            $resources = new Resources;

            foreach ($files as $file) {
                $resources->addFileResource($file);
            }
        }

        $this->resources = $resources;
    }

    /**
     * Loads the main resources and caches the results.
     *
     * If debuggin, all included resources will be put into a manifest file to
     * keep track of their changes.
     *
     * @return void
     */
    private function doLoadResources()
    {
        $collection = new Routes;

        foreach ($this->resources->getResources() as $i => $resource) {
            // dont't add the prmary resource to
            $collection->merge($this->loader->load($this->current = (string)$resource));
        }

        if ($this->isDebugging()) {
            $this->writeManifests();
        }

        $this->current = null;
        $this->storage->write($collection);
    }

    /**
     * writeManifests
     *
     * @return void
     */
    private function writeManifests()
    {
        foreach ($this->resources->getResources() as $resource) {
            $manifest = $this->getManifestFileName($file = (string)$resource);
            $this->ensureCollector($file);

            $this->writeManifest($manifest, $this->meta[$file]);
        }
    }

    /**
     * writeManifest
     *
     * @param string $file
     * @param CollectorInterface $resources
     * @throws \RuntimeException if creation of cache dir fails.
     *
     * @return void
     */
    private function writeManifest($file, CollectorInterface $resources)
    {
        $mask = 0755 & ~umask();

        if (!is_dir($dir = dirname($file))) {
            if (false === @mkdir($dir, $mask, true)) {
                throw new \RuntimeException('Creating manifest for router cache failed.');
            }
        } elseif (false === @chmod($dir, $mask)) {
            throw new \RuntimeException('Cannot apply permissions on cache directory.');
        }

        file_put_contents($file, serialize($resources), LOCK_EX);
    }

    /**
     * ensureCollector
     *
     * @param mixed $file
     *
     * @return void
     */
    private function ensureCollector($file, $manifest = null)
    {
        if (!isset($this->meta[$file])) {
            if (null !== $manifest && file_exists($manifest)) {
                $this->meta[$file] = unserialize(file_get_contents($manifest));
            } else {
                $this->meta[$file] = new Collector;
            }
        }
    }

    /**
     * validateManifest
     *
     * @return void
     */
    private function validateManifest()
    {
        foreach ($this->resources->getResources() as $resource) {
            $file = $this->getManifestFileName((string)$resource);

            if (!file_exists($file)) {
                return false;
            }

            $time = filemtime($file);
            $manifest = unserialize(file_get_contents($file));

            if (!$manifest->isValid($time)) {
                return false;
            }
        }

        return true;
    }

    /**
     * getManifestFileName
     *
     * @param string $file
     *
     * @return string
     */
    private function getManifestFileName($file)
    {
        if (!isset($this->metaCache[$file])) {
            $ds = DIRECTORY_SEPARATOR;

            $name = substr_replace(
                substr_replace(md5_file($file) . '_'.basename($file) . '.manifest', $ds, 4, 0),
                $ds,
                2,
                0
            );

            $this->metaCache[$file] = sprintf('%s%s%s', $this->manifest, $ds, $name);
        }

        return $this->metaCache[$file];
    }

    /**
     * getManifest
     *
     * @return string
     */
    private function getManifest()
    {
        return $this->manifest;
    }
}
