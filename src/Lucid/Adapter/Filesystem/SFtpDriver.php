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
use Lucid\Module\Filesystem\Mime\MimeType;
use Lucid\Module\Filesystem\FilesystemInterface;
use Lucid\Adapter\Filesystem\Sftp\Connection as SftpConnection;
use Lucid\Adapter\Filesystem\FtpConnectionInterface as Connection;

/**
 * @class SFtpDriver
 *
 * @package Lucid\Adapter\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class SFtpDriver extends AbstractFtp
{

    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        return $this->getConnection()->file_exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        return $this->getConnection()->is_file($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        return $this->getConnection()->is_dir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isLink($path)
    {
        return $this->getConnection()->is_link($path);
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $stream, $offset = null, $maxlen = null)
    {
        $this->ensureDirectory(dirname($path));

        $bytes = $this->doWriteStream($path, $stream, $offset, $maxlen);

        if (false !== $bytes  && $this->getConnection()->chmod($this->filePermission(), $path, false)) {
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
        return $this->getConnection()->rename($source, $target);
    }

    /**
     * {@inheritdoc}
     */
    public function createDirectory($dir, $permission = null, $recursive = true)
    {
        return $this->getConnection()->mkdir($dir, $permission ?: $this->directoryPermissions(), $recursive);
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

        return null !== $contents ? mb_strlen($contents) : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile($path, $contents = null)
    {
        if (!$connection->put($path, $contents, NET_SFTP_STRING)) {
            return false;
        }

        return null !== $contents ? mb_strlen($contents) : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile($file)
    {
        return $this->getConnection()->delete($file);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDirectory($dir)
    {
        return $this->getConnection()->delete($dir, true);
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

    protected function statToPathInfo($path, $stat)
    {
        $result['path'] = $path;
        $result['type'] = $this->getObjectType($stat['type']);
        $result['timestamp'] = $stat['mtime'];

        if ('file' === $result['type']) {
            $result['size'] = $stat['size'];
        }

        $result['permission'] = $this->filePermsAsString($stat['permissions']);
        $result['visibility'] = $this->getVisibilityFromMod($stat['permissions']);


        return $this->createPathInfo($result);
    }

    /**
     * {@inheritdoc}
     */
    public function listDirectory($dir, $recursive = false)
    {
        if (!$contents = $this->doListDirectory($dir, (bool)$recursive, $this->getConnection())) {
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
    }

    /**
     * {@inheritdoc}
     */
    public function copyDirectory($dir, $target)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission($path, $mod, $recursive = true)
    {
        return $this->getConnection()->chmod($mod, $path, (bool)$recursive);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission($path)
    {
        if ($stat = $this->getConnection()->stat($path)) {
            $permission = $this->filePermsAsString($stat['permissions']);
            $visibility = $this->getVisibilityFromMod($stat['permissions']);

            return compact('permission', 'visibility');
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

        if (!$list = $conn->rawlist($dir)) {
            return false;
        }

        foreach ($list as $filename => $object) {
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

    /**
     * getObjectType
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
    protected function setupConnection(Connection $connection = null)
    {
        if (null === $connection) {
            $creds = array_intersect_key(
                $this->options,
                array_flip(['host', 'port', 'user', 'password', 'private_key', 'timeout'])
            );

            $this->connection = new SftpConnection($creds);
        } else {
            $this->connection = $connection;
        }
    }

    /**
     * defaultOptions
     *
     * @return array
     */
    protected static function defaultOptions()
    {
        return [
            'host' => '',
            'port' => 22,
            'user' => '',
            'password' => '',
            'force_detect_mime' => false,
            'directory_permission' => 0755,
            'file_permission' => 0664,
        ];
    }
}
