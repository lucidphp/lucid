<?php

/*
 * This File is part of the Lucid\Mux\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Cache\Matcher;

use DateTime;
use RuntimeException;
use Lucid\Mux\Cache\CachedCollectionInterface;

/**
 * @class MapWriter
 *
 * @package Lucid\Mux\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MapLoader
{
    /** @var string */
    const DEFAULT_PREFIX = 'map_';

    /** @var Dumper */
    private $dumper;

    /** @var string */
    private $dumpPath;

    /** @var string */
    private $prefix;

    /**
     * @param Dumper $dumper
     * @param string $dumpPath
     * @param string $prefix
     */
    public function __construct(Dumper $dumper, $dumpPath, $prefix = self::DEFAULT_PREFIX)
    {
        $this->dumper   = $dumper;
        $this->dumpPath = $dumpPath;
        $this->prefix   = $prefix;
    }

    /**
     * Get the statuc route map.
     *
     * @param CachedCollectionInterface $routes.
     *
     * @return array
     */
    public function load(CachedCollectionInterface $routes)
    {
        if (!is_file($file = $this->getFilePath($routes)) || filemtime($file) < $routes->getTimestamp()) {
            $this->dumpMap($routes, $file);
        }

        return $this->loadMap($file);
    }

    /**
     * getFilePath
     *
     * @param CachedCollectionInterface $routes
     *
     * @return string
     */
    private function getFilePath(CachedCollectionInterface $routes)
    {
        $name = $this->prefix . hash('sha1', $time = $routes->getTimestamp());
        return $this->dumpPath . DIRECTORY_SEPARATOR . $name . '.php';
    }

    /**
     * dumpMap
     *
     * @param CachedCollectionInterface $routes
     * @param string $file the php file.
     *
     * @throws RuntimeException if path is invalid.
     *
     * @return void
     */
    private function dumpMap(CachedCollectionInterface $routes, $file)
    {
        // try to create dump path.
        if (!is_dir($path = dirname($file))) {
            throw new RuntimeException(
                sprintf('Cannot create route map "%s" at "%s". No such directory.', basename($file), $path)
            );
        }

        file_put_contents($file, $this->dumper->dump($routes));
    }

    /**
     * Loads the static route map.
     *
     * @return array
     */
    private function loadMap($file)
    {
        return require $file;
    }
}
