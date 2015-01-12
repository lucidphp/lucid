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

use Net_SSH2;
use Net_SFTP;
use Lucid\Module\Filesystem\Permission;
use Lucid\Module\Filesystem\Mime\MimeType;
use Lucid\Module\Filesystem\FilesystemInterface;
use Lucid\Module\Filesystem\Driver\SupportsTouch;
use Lucid\Module\Filesystem\Driver\SupportsVisibility;
use Lucid\Module\Filesystem\Driver\SupportsPermission;
use Lucid\Adapter\Filesystem\Sftp\Connection as SftpConnection;
use Lucid\Adapter\Filesystem\FtpConnectionInterface as Connection;
use Lucid\Adapter\Filesystem\Traits\StatCacheTrait;

/**
 * @class SFtpDriver
 *
 * @package Lucid\Adapter\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SFtpDriver extends AbstractFtp implements SupportsTouch, SupportsVisibility, SupportsPermission
{
    use StatCacheTrait;

    /**
     * connKeys
     *
     * @var array
     */
    protected static $connKeys = [
        'host', 'port', 'user',
        'password', 'private_key', 'timeout'
    ];

    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        if ($stat = $this->getStat($path)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        //return $this->getConnection()->is_file($path);
        if ($stat = $this->getStat($path)) {
            return NET_SFTP_TYPE_REGULAR === $stat['type'];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        //return $this->getConnection()->is_dir($path);
        if ($stat = $this->getStat($path)) {
            return NET_SFTP_TYPE_DIRECTORY === $stat['type'];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isLink($path)
    {
        //return $this->getConnection()->is_dir($path);
        if ($stat = $this->getStat($path)) {
            return NET_SFTP_TYPE_SYMLINK === $stat['type'];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function touch($path, $mtime = null, $atime = null)
    {
        return $this->getConnection()->touch($path, $mtime, $atime);
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $stream, $offset = null, $maxlen = null)
    {
        $this->ensureDirectory(dirname($path));

        $bytes = $this->doWriteStream($path, $stream, $offset, $maxlen);

        if (false !== $bytes && $this->doSetPermission($path, $this->filePermission(), true, false)) {
            return $bytes;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $stream, $offset = null, $maxlen = null)
    {
        return $this->doWriteStream($path, $stream, $offset, $maxlen);
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($path, $offset = null, $maxlen = null)
    {
        $offset =  null !== $offset ? $offset : 0;
        $maxlen =  null !== $maxlen ? $maxlen : -1;

        return $this->getConnection()->get($path, false, $offset, $maxlen);
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        if (false !== $this->getConnection()->get($path, $tmp = $this->getTempfile())) {
            rewind($tmp);
            return $tmp;
        }

        @fclose($tmp);

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rename($source, $target)
    {
        if ($this->getConnection()->rename($source, $target)) {
            $this->reStat($source, $target);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function createDirectory($dir, $permission = null, $recursive = true)
    {
        if ($this->hasStat($dir)) {
            return false;
        }

        return $this->getConnection()->mkdir($dir, $permission ?: $this->directoryPermission(), $recursive);
    }

    /**
     * {@inheritdoc}
     */
    public function writeFile($path, $contents = null)
    {
        $connection = $this->getConnection();
        $this->ensureDirectory(dirname($path));

        if (!$connection->put($path, $contents, NET_SFTP_STRING)) {
            return false;
        }

        if (!$connection->chmod($this->filePermission(), $path, false)) {
            return false;
        }

        return $this->contentSize($contents ?: '');
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile($path, $contents = null)
    {
        if (!$connection->put($path, $contents, NET_SFTP_STRING)) {
            return false;
        }

        return $this->contentSize($contents ?: '');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile($file)
    {
        if ($this->getConnection()->delete($file)) {
            $this->clearStat($file);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDirectory($dir)
    {
        if ($this->getConnection()->delete($dir, true)) {
            $this->clearStat($file);

            return true;
        }

        return false;
    }

    /**
     * getPathInfo
     *
     * @param string $path
     *
     * @return void
     */
    public function getPathInfo($path)
    {
        $connection = $this->getConnection();

        if (false === ($stat = $connection->stat($path))) {
            return false;
        }

        return $this->statToPathInfo($path, $stat);

    }

    /**
     * {@inheritdoc}
     */
    public function listDirectory($dir, $recursive = false)
    {
        if (false === $contents = $this->doListDirectory($dir, (bool)$recursive, $this->getConnection())) {
            return false;
        }

        return $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFile($path)
    {
        if ($this->isFile($path)) {
            return true;
        }

        if (false !== $this->writeFile($path, null)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function ensureDirectory($path)
    {
        if (!$this->isDir($path) && !$this->createDirectory($path, null, true)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile($file, $target)
    {
        if (!$mode = $this->getConnection()->fileperms($file)) {
            return false;
        }

        return $this->doCopyFile($file, $target, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission($path, $mod, $recursive = true)
    {
        return $this->doSetPermission($path, $mod, $this->isFile($path), $recursive);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission($path)
    {
        if ($perms = $this->getConnection()->fileperms($path)) {
            return new Permission($perms & 0777);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType($file)
    {
        if (!is_string($cnt = $this->readFile($file, 0, 16))) {
            return false;
        }

        if (!$this->getOption('force_detect_mime')) {
            return MimeType::getFromExtension($file);
        }

        if (MimeType::defaultType() === ($mime = MimeType::getFromContent($cnt)) && 0 !== $pos = strrpos($file, '.')) {
            return MimeType::getFromExtension($file);
        }

        return $mime;
    }

    /**
     * doSetPermission
     *
     * @param string $path
     * @param int $mod
     * @param boolean $isFile
     * @param boolean $recursive
     *
     * @return boolean
     */
    protected function doSetPermission($path, $mod, $isFile = true, $recursive = false)
    {
        return $this->getConnection()->chmod($mod, $path, $isFile ? true : (bool)$recursive);
    }

    /**
     * Convert a list response to a pathinfo array/object
     *
     * @param string $path
     * @param array $stat
     *
     * @return void
     */
    protected function statToPathInfo($path, array $stat)
    {
        $info['type'] = $this->getObjectType($stat['type']);
        $info['path'] = $path;
        $info['timestamp'] = $stat['mtime'];

        if ('file' === $info['type']) {
            $info['size'] = $stat['size'];
        }

        $this->setInfoPermission($info, $stat['permissions'] & 0777);

        return $this->createPathInfo($info);
    }

    /**
     * {@inheritdoc}
     */
    protected function uploadStream($path, $stream)
    {
        if (!$this->getConnection()->put($path, $stream)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doCopyFile($file, $target, $perm)
    {
        if (!$stream = $this->readStream($file)) {
            return false;
        }

        $ret = $this->doWriteStream($target, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        if (!$this->doSetPermission($target, $this->getConnection()->fileperms($file), true, false)) {
            return false;
        }

        return $ret;
    }

    /**
     * doListDirectory
     *
     * @param mixed $dir
     * @param mixed $recursive
     * @param Net_SFTP $conn
     * @param array $contents
     *
     * @return array
     */
    protected function doListDirectory($dir, $recursive, Net_SFTP $conn, $parent = null, array &$contents = [])
    {
        if (false === $stat = $this->getStat($path)) {
            return false;
        }

        foreach ($stat as $filename => $object) {
            if (in_array($filename, ['.', '..'])) {
                continue;
            }

            $key = $parent ? $parent . $this->directorySeparator . $filename : $filename;
            $path = 0 === strlen($dir) ? $filename : $dir . $this->directorySeparator . $filename;
            $contents[$key] = $this->statToPathInfo($path, (array)$object);

            if (false !== $recursive && 'dir' === $this->getObjectType($object['type'])) {
                $prefix =  $parent ? $parent . $this->directorySeparator. $filename : $filename;
                $this->doListDirectory($path, $recursive, $conn, $prefix, $contents);
            }
        }

        return $contents;
    }

    protected function statPath($dir)
    {
        if ($list = $conn->rawlist($dir)) {
            return $list;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsLink()
    {
        return true;
    }

    /**
     * Get the object file type returned by the Net_SFTP List response.
     *
     * @param int $type
     *
     * @return string
     */
    protected function getObjectType($type)
    {
        switch ($type) {
            case NET_SFTP_TYPE_REGULAR:
                return 'file';
            case NET_SFTP_TYPE_DIRECTORY:
                return 'dir';
            case NET_SFTP_TYPE_SYMLINK:
                return 'link';
        }

        return 'unknowen';
    }

    /**
     * {@inheritdoc}
     */
    protected function setupConnection(Connection $connection = null, array $creds = [])
    {
        if (null === $connection) {
            $this->connection = new SftpConnection($creds);
        } else {
            $this->connection = $connection;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected static function defaultOptions()
    {
        return array_merge(parent::defaultOptions(), [
            'host' => '',
            'port' => 22,
            'user' => '',
            'password' => '',
        ]);
    }
}
