<?php

/*
 * This File is part of the Lucid\Adapter\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Filesystem;

use Sabre\DAV\Client;
use Lucid\Module\Filesystem\Permission;
use Lucid\Module\Filesystem\Mime\MimeType;
use Lucid\Module\Filesystem\Driver\AbstractDriver;

/**
 * @class WebDavDriver
 *
 * @package Lucid\Adapter\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class WebDavDriver extends AbstractDriver
{
    /**
     * client
     *
     * @var Client
     */
    private $client;

    /**
     * Constructor.
     *
     * @param Client $client
     * @param string $mount
     */
    public function __construct(Client $client, $mount = null)
    {
        $this->client = $client;
        parent::__construct($mount);
    }

    public function exists($path)
    {

    }

    public function isFile($path)
    {

    }

    public function isLink($path)
    {
        return false;
    }

    public function isDir($path)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function writeFile($file, $contents = null)
    {
        if ($this->client->put($this->getPrefixed($file, $contents ?: ''))) {
            return $this->contentSize($content ?: '');
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile($file, $contents = null)
    {
        return $this->writeFile($file, $contents);
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($path, $offset = null, $maxlen = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $stream, $offset = null, $maxlen = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $stream, $offset = null, $maxlen = null)
    {
    }

    public function readStream($path)
    {
    }

    public function createDirectory($dir, $permission = 0755, $recursive = true)
    {
    }

    public function deleteFile($file)
    {
    }

    public function deleteDirectory($dir)
    {
    }

    public function rename($source, $target)
    {
    }

    public function copyFile($file, $target)
    {
    }

    public function copyDirectory($dir, $target)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission($path, $mod, $recursive = true)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission($path)
    {
        return new Permission(null, Permission::V_PUBLIC);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType($file)
    {
        $info = $this->statPath($path);

        return $info['mimetype'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo($path)
    {
        $info = $this->statPath($path);

        return $this->createPathInfo($info);
    }

    /**
     * {@inheritdoc}
     */
    public function listDirectory($path, $recursive = true)
    {
        if ($result = $this->doListDirectory($path, $recursive)) {
            return $result;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFile($path)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function ensureDirectory($path)
    {
    }

    /**
     * statPath
     *
     * @param string $path
     *
     * @return array
     */
    protected function statPath($path)
    {
        return $this->parseClientResponse($path, $this->client->propfind($this->getPrefixed($path), []));

    }

    /**
     * doListDirectory
     *
     * @param mixed $path
     * @param mixed $recursive
     * @param array $result
     * @param mixed $parent
     *
     * @return void
     */
    protected function doListDirectory($path, $recursive = false, array &$result = [], $parent = null)
    {
        if (!$stat = $this->client->propfind($loc = $this->getPrefixed($path), [
            '{DAV:}displayname',
            '{DAV:}getcontentlength',
            '{DAV:}getcontenttype',
            '{DAV:}getlastmodified',
            ], 1)) {

            return false;
        }

        // remove self:
        array_shift($stat);

        foreach ($stat as $pathName => $object) {
            $nPath = $this->findPathOrigin($loc, urldecode($pathName));
            $info = $this->parseClientResponse($nPath, $object);

            $sp = $this->directorySeparator;
            $bn = basename($nPath);
            $key = $parent ? $parent . $sp . $bn : $bn;

            $result[$key] = $this->createPathInfo($info);

            if ($recursive && 'dir' === $info['type']) {
                $prefix =  $parent ? $parent . $sp . $bn : $bn;
                $this->doListDirectory($nPath, $recursive, $result, $prefix);
            }
        }

        return $result;
    }

    /**
     * findPathOrigin
     *
     * @param mixed $root
     * @param mixed $path
     *
     * @return void
     */
    protected function findPathOrigin($root, $path)
    {
        if (false !== $pos = strpos($path, $root)) {
            $nPath = substr($path, $pos);

            return trim($this->getUnprefixed($nPath), $this->directorySeparator);
        }

        return $path;
    }

    /**
     * parseClientResponse
     *
     * @param string $path
     * @param array $stat
     *
     * @return array
     */
    protected function parseClientResponse($path, array $stat)
    {
        $info = ['path' => $path, 'type' => 'dir', 'permission' => null, 'visibility' => Permission::V_PUBLIC];
        $info['timestamp'] = strtotime($stat['{DAV:}getlastmodified']);

        if (isset($stat['{DAV:}getcontentlength'])) {
            $info['type'] = 'file';
            $info['size'] = (int)$stat['{DAV:}getcontentlength'];
            $info['mimetype'] = $stat['{DAV:}getcontenttype'];
        }

        return $info;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPrefixed($path)
    {
        $sp = $this->directorySeparator;

        return trim(parent::getPrefixed($path), $sp);
    }
}
