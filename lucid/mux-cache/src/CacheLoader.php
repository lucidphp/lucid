<?php

/*
 * This File is part of the Lucid\Routing\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache;

use RuntimeException;
use Lucid\Mux\Routes;
use Lucid\Mux\RouteCollectionInterface;
use Lucid\Mux\Cache\Dumper;
use Lucid\Resource\ResourceInterface;
use Lucid\Resource\Loader\LoaderInterface;
use Lucid\Mux\Loader\LoaderInterface as RouteLoaderInterface;
use Lucid\Resource\Loader\ListenerInterface;
use Lucid\Resource\Collection as Resources;
use Lucid\Resource\CollectionInterface as ResourcesInterface;

/**
 * @class RouterCache
 *
 * @package Selene\Package\Framework\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CacheLoader implements ListenerInterface
{
    /** @var ResourceInterface */
    private $current;

    /** @var bool */
    private $debug;

    /** @var string */
    private $manifest;

    /** @var ResourcesInterface */
    private $resources;

    /** @var StorageInterface */
    private $storage;

    /** @var array */
    private $meta;

    /** @var array */
    private $metaCache;

    /**
     * Constructor.
     *
     * @param mixed $resources
     * @param Storage $storage
     * @param stringixed $manifest
     * @param bool $debug
     */
    public function __construct($resources, StorageInterface $storage, $manifest = 'routes', $debug = true)
    {
        $this->manifest  = $manifest;
        $this->debug     = $debug;

        $this->meta      = [];
        $this->metaCache = [];
        $this->storage = $storage;

        $this->setResources($resources);
    }

    /**
     * Checks if the cache is valid.
     *
     * @param bool $forceValidate;
     *
     * @return bool
     */
    private function isValid($forceValidate)
    {
        if ($this->storage->exists() && $this->resources->isValid($this->storage->getLastWriteTime())) {
            return ($forceValidate || $this->isDebugging()) ? $this->validateManifest() : true;
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
    public function load(RouteLoaderInterface $loader, $forceValidate = false)
    {
        if (!$this->isValid($forceValidate)) {
            $loader->addListener($this);
            $this->doLoadResources($loader, $forceValidate);
            $loader->removeListener($this);
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
    public function onLoaded(ResourceInterface $resource)
    {
        if (!$this->isDebugging() || $this->current === $resource) {
            return;
        }

        $this->ensureCollector($this->current);
        $this->meta[$this->current]->addResource($resource);
    }

    /**
     * Check if in debug mode.
     *
     * @return bool
     */
    private function isDebugging()
    {
        return $this->debug;
    }

    /**
     * Set the main resource files.
     *
     * @param string|array|ResourcesInterface $resources
     *
     * @return void
     */
    private function setResources($resources)
    {
        if (!$resources instanceof ResourcesInterface) {
            $files = (array)$resources;
            $resources = new Resources;

            array_walk($files, [$resources, 'addFileResource']);
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
    private function doLoadResources(RouteLoaderInterface $loader, $forceValidate)
    {
        $collection = [];

        foreach ($this->resources->all() as $i => $resource) {
            // dont't add the prmary resource to
            $this->current = (string)$resource;

            if ($loaded = $loader->loadRoutes($resource)) {
                $collection[] = $loaded->all();
            }
        }

        $routes = call_user_func_array('array_merge', $collection);

        if ($this->isDebugging() || $forceValidate) {
            $this->writeManifests();
        }

        $this->current = null;
        $this->storage->write(new Routes($routes));
    }

    /**
     * writeManifests
     *
     * @return void
     */
    private function writeManifests()
    {
        foreach ($this->resources->all() as $resource) {
            $manifest = $this->getManifestFileName($file = (string)$resource);
            $this->ensureCollector($file);
            $this->writeManifest($manifest, $this->meta[$file]);
        }
    }
    /**
     * writeManifest
     *
     * @param string $file
     * @param ResourcesInterface $resources
     * @throws RuntimeException if creation of cache dir fails.
     *
     * @return void
     */
    private function writeManifest($file, ResourcesInterface $resources)
    {
        $mask = 0775 & ~umask();
        if (!is_dir($dir = dirname($file))) {
            if (false === mkdir($dir, $mask, true)) {
                throw new RuntimeException('Creating manifest for router cache failed.');
            }
        } elseif (false === @chmod($dir, $mask)) {
            throw new RuntimeException('Cannot apply permissions on cache directory.');
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
                $this->meta[$file] = new Resources;
            }
        }
    }
    /**
     * validateManifest
     *
     * @return bool
     */
    private function validateManifest()
    {
        foreach ($this->resources->all() as $resource) {
            if (!file_exists($file = $this->getManifestFileName((string)$resource))) {
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
            $base = sha1_file($file) . '_'.basename($file) . '.manifest';
            $name = substr_replace(substr_replace($base, $ds, 4, 0), $ds, 2, 0);

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
