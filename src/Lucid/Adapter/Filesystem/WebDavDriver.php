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

    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        return (bool)$this->statPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        if ($info = $this->statPath($path)) {
            return 'file' === $info['type'];
        }

        return false;

    }

    /**
     * {@inheritdoc}
     */
    public function isLink($path)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        if ($info = $this->statPath($path)) {
            return 'dir' === $info['type'];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function writeFile($file, $contents = null)
    {
        if ($this->client->request('PUT', $this->getPrefixed($file, $contents ?: ''))) {
            return $this->contentSize($contents ?: '');
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
        try {
            $ret = $this->client->request('GET', $this->getPrefixed($path));
            if (200 !== $ret['statusCode']) {
                return false;
            }

        } catch (\Exception $e) {
            return false;
        }

        $max = (int)current($ret['headers']['content-length']);
        $offset = $offset !== null ? (int)$offset : 0;
        $maxlen = $maxlen !== null ? min($max, (int)$maxlen) : $max;

        return mb_substr($ret['body'], $offset, $maxlen, '8bit');
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $stream, $offset = null, $maxlen = null)
    {
        $offset = null !== $offset ? (int)$offset : -1;
        $maxlen = null !== $maxlen ? (int)$maxlen : -1;

        try {
            $ret = $this->client->request('PUT', $this->getPrefixed($path), $cnt = stream_get_contents($stream, $maxlen, $offset));
            if (!in_array($ret['statusCode'], [201, 204])) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $this->contentSize($cnt);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $stream, $offset = null, $maxlen = null)
    {
        return $this->writeStream($path, $stream, $offset, $maxlen);
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        if (false === $contents = $this->readFile($path)) {
            return false;
        }

        $stream = tmpfile();
        fwrite($stream, $contents);
        rewind($stream);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function createDirectory($dir, $permission = null, $recursive = true)
    {
        $ret = $this->client->request('MKCOL', $this->getPrefixed($dir));
        if (201 !== $ret['statusCode']) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile($file)
    {
        return $this->deleteObject($this->getPrefixed($file));
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDirectory($dir)
    {
        return $this->deleteObject($this->getPrefixed($dir));
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteObject($path)
    {
        $ret = $this->client->request('DELETE', $path);
        if (204 !== $ret['statusCode']) {
            return false;
        }

        return true;
    }

    public function rename($source, $target)
    {
        if ($this->exists($target)) {
            return false;
        }

        $info = (parse_url($this->client->getAbsoluteUrl($this->getPrefixed(''))));
        $sp = $this->directorySeparator;
        $ret = $this->client->request(
            'MOVE',
            ltrim($this->getPrefixed($source), $sp),
            null,
            ['Destination' => $info['path'].ltrim($this->getPrefixed($target), $sp)]
        );

        if (201 !== $ret['statusCode']) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile($file, $target)
    {
        if (false !== $stream = $this->readStream($file)) {
            rewind($stream);
            $bytes = $this->writeStream($target, $stream);
            fclose($stream);

            return $bytes;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function copyDirectory($dir, $target)
    {
        if (false === $stat = $this->listDirectory($dir, false)) {
            return false;
        }

        if (!$this->createDirectory($target)) {
            return false;
        }

        $sp = $this->directorySeparator;
        $bytes = 0;

        foreach ($stat as $relPath => $info) {

            $basename = basename($info['path']);
            $pName = $info['path'];
            $tName = $target.$sp.$basename;

            if ('file' === $info['type']) {
                if (false !== $ret = $this->copyFile($info['path'], $tName)) {
                    $bytes += $ret;
                    continue;
                }
            }

            if ('dir' === $info['type']) {
                if (false !== $ret = $this->copyDirectory($info['path'], $tName)) {
                    $bytes += $ret;
                    continue;
                }
            }

            return false;
        }

        return $bytes;
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
        if (is_array($result = $this->doListDirectory($path, $recursive))) {
            return $result;
        }


        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFile($path)
    {
        if ($this->isFile($path)) {
            return true;
        }

        return (bool)$this->writeFile($path, null);
    }

    /**
     * {@inheritdoc}
     */
    public function ensureDirectory($path)
    {
        if ($this->isDir($path)) {
            return true;
        }

        return $this->createDirectory($path, true);
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
