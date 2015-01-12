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

use Dropbox\Client;
use Dropbox\WriteMode;
use Dropbox\Exception as DboxException;
use Lucid\Module\Filesystem\Permission;
use Lucid\Module\Filesystem\Mime\MimeType;
use Lucid\Module\Filesystem\FilesystemInterface;
use Lucid\Module\Filesystem\Driver\AbstractDriver;
use Lucid\Module\Filesystem\Driver\SupportsVisibility;
use Lucid\Module\Filesystem\Driver\DriverInterface;

/**
 * @class DropBoxDriver
 *
 * @package Lucid\Adapter\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class DropboxDriver extends AbstractDriver implements SupportsVisibility
{
    /**
     * client
     *
     * @var Client
     */
    private $client;

    /**
     * responseMap
     *
     * @var array
     */
    protected static $responseMap = [
        'mime_type' => 'mimetype',
        'bytes' => 'size'
    ];

    /**
     * Construct.
     *
     * @param Client $client
     * @param mixed $mount
     */
    public function __construct(Client $client, $mount = null, array $options = [])
    {
        $this->client = $client;
        $this->options = array_merge(static::defaultOptions(), $options);
        parent::__construct($mount);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPrefixed($path)
    {
        $sp = $this->directorySeparator;

        return $sp.trim(parent::getPrefixed($path), $sp);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        return null !== $this->client->getMetaData($this->getPrefixed($path));
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        if (null !== $res = $this->client->getMetaData($this->getPrefixed($path))) {
            return $res['is_dir'];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        if (null !== $res = $this->client->getMetaData($this->getPrefixed($path))) {
            return false === $res['is_dir'];
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
    public function getPathInfo($path)
    {
        return $this->createPathInfo(
            $this->parseClientResponse($this->client->getMetaData($this->getPrefixed($path)), $path)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function writeFile($path, $contents = null, $offset = null, $maxlen = null)
    {
        return $this->uploadFile($this->getPrefixed($path), $contents, WriteMode::force(), $offset, $maxlen);
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile($path, $contents = null, $offset = null, $maxlen = null)
    {
        return $this->uploadFile($this->getPrefixed($path), $contents, WriteMode::force(), $offset, $maxlen);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $stream, $offset = null, $maxlen = null)
    {
        return $this->uploadStream($this->getPrefiexd($path), $stream, WriteMode::force(), $offset, $maxlen);
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $stream, $offset = null, $maxlen = null)
    {
        return $this->uploadStream($this->getPrefixed($path), $stream, WriteMode::force(), $offset, $maxlen);
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($path, $offset = null, $maxlen = null)
    {
        if (false === $stream = $this->readStream($path)) {
            return false;
        }

        $contents = stream_get_contents($stream, null === $maxlen ? -1 : $maxlen, null === $offset ? -1 : $offset);

        fclose($stream);

        return $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        $stream = tmpfile();

        if (null === $this->client->getFile($this->getPrefixed($path), $stream)) {
            fclose($stream);

            return false;
        }

        rewind($stream);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function createDirectory($path, $permission = 0755, $recursive = true)
    {
        try {
            $res = $this->client->createFolder($loc = $this->getPrefixed($path));

        } catch (DboxException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function listDirectory($path, $recursive = false)
    {
        if ($res = $this->doListDir($this->getPrefixed($path), $recursive)) {
            return $res;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission($path, $mode = 0755, $recursive = true)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission($path)
    {
        if ($this->exists($path)) {
            return new Permission(null, Permission::V_PUBLIC);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $target)
    {
        try {
            $this->client->move($this->getPrefixed($path), $this->getPrefixed($target));
        } catch (DboxException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function copyDirectory($dir, $target)
    {
        return $this->copyObject($this->getPrefixed($dir), $this->getPrefixed($target));
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile($file, $target)
    {
        return $this->copyObject($this->getPrefixed($file), $this->getPrefixed($target));
    }

    /**
     * copyObject
     *
     * @param mixed $source
     * @param mixed $target
     *
     * @return void
     */
    protected function copyObject($source, $target)
    {
        try {
            $this->client->copy($source, $target);
        } catch (DboxException $e) {
            return false;
        }

        return true;
    }

    protected function deleteObject($source)
    {
        try {
            $this->client->delete($source);
        } catch (DboxException $e) {
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
    public function ensureDirectory($dir)
    {
        if ($this->isDir($dir)) {
            return true;
        }

        return $this->createDirectory($dir);
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFile($file)
    {
        if ($this->isFile($file)) {
            return true;
        }

        return $this->writeFile($file, null);
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
    public function getMimeType($path)
    {
        if (null === ($res = $this->client->getMetaData($loc = $this->getPrefixed($path)))) {
            return false;
        }

        $mime = $res['mime_type'];

        if (!$this->getOption('force_detect_mime')) {
            return $mime;
        } elseif (2048 < $res['bytes']) {
            return $mime;
        }

        $stream = tmpfile();

        stream_set_chunk_size($stream, 8);

        if (null === $this->client->getFile($loc, $stream)) {
            fclose($stream);

            return $mime;
        }

        rewind($stream);

        $cnt = stream_get_contents($stream);

        if (MimeType::defaultType() === ($nMime = MimeType::getFromContent($cnt))) {
            return $mime;
        }

        return $nMime;
    }

    /**
     * uploadFile
     *
     * @param string $path
     * @param string $contents
     * @param WriteMode $mode
     *
     * @return array|boolean
     */
    protected function uploadFile($path, $contents, WriteMode $mode, $offset = null, $maxlen = null)
    {
        if (null !== ($res = $this->client->uploadFileFromString($path, $this->trimContent($contents ?: '', $offset, $maxlen), $mode))) {
            return $res['bytes'];
        }

        return false;
    }

    /**
     * trimContent
     *
     * @param mixed $content
     * @param mixed $offset
     * @param mixed $maxlen
     *
     * @return stirng
     */
    protected function trimContent($content, $offset, $maxlen)
    {
        if (null === $content || 0 === $size = $this->contentSize($content) || (null === $offset && null === $maxlen)) {
            return $content;
        }

        $start  = (int)$offset ?: 0;

        return mb_substr($content, $start, (int)$maxlen < ($this->contentSize($content) - $start) ? (int)$maxlen : $size, '8bit');

        //if (null !== $offset) {
        //    $content = mb_substr($content, (int)$offset, $size, '8bit');
        //}

        //if (null !== $maxlen && (int)$maxlen < $this->contentSize($content)) {
        //    return mb_substr($content, 0, (int)$maxlen, '8bit');
        //}

        //return $content;
    }

    /**
     * uploadStream
     *
     * @param mixed $path
     * @param mixed $stream
     * @param WriteMode $mode
     *
     * @return PathInfo|array|boolean
     */
    protected function uploadStream($path, $stream, WriteMode $mode, $offset = null, $size = null)
    {
        if (null !== $offset) {
            fseek($stream, ftell($stream) + (int)$offset);
        }

        if (null !== $res = $this->client->uploadFile($path, $mode, $stream, $size)) {
            return $res['bytes'];
        }

        return false;
    }

    /**
     * doListDir
     *
     * @param mixed $path
     * @param mixed $recursive
     *
     * @return array|boolean
     */
    protected function doListDir($path, $recursive)
    {
        if (null === $data = $this->client->getMetadataWithChildren($path)) {
            return false;
        }

        $res = [];

        foreach ($data as $metaData) {
            $res[$key = $this->unPrefixed($metaData['path'])] = $this->parseClientResponse($metaData, $key);

            if ($recursive && $metaData['is_dir']) {
                $res = $res + $this->doListDir($metaData['path'], $recursive);
            }
        }

        return $res;
    }

    /**
     * Parse the dropbox client response into a usable array.
     *
     * @param array $response
     * @param string|null $path
     *
     * @return void
     */
    protected function parseClientResponse(array $response, $path = null, array $data = [])
    {
        $result = [
            'path' => $path ?: $response['path'],
            'timestamp' => isset($response['modified']) ? strtotime($response['modified']) : null
        ];

        foreach (static::$responseMap as $key => $newKey) {
            if (isset($response[$key])) {
                $result[$newKey] = $response[$key];
            }
        }

        $result['type'] = $response['is_dir'] ? 'dir': 'file';

        return array_merge($data, $result);
    }

    /**
     * compactPermission
     *
     * @return void
     */
    protected function compactPermission()
    {
        return [
            'permission' => null,
            'visibility' => FilesystemInterface::PERM_PUBLIC
        ];
    }
}
