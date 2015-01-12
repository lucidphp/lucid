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

use Lucid\Module\Filesystem\Mime\MimeType;
use Lucid\Module\Filesystem\Permission;
use Lucid\Module\Filesystem\FilesystemInterface;
use Lucid\Module\Filesystem\Driver\SupportsVisibility;
use Lucid\Module\Filesystem\Driver\SupportsPermission;
use Lucid\Adapter\Filesystem\Ftp\Connection as FtpConnection;
use Lucid\Adapter\Filesystem\FtpConnectionInterface as Connection;

/**
 * @class FtpDriver
 * @see SupportsVisibility
 * @see SupportsPermission
 * @see AbstractFtp
 *
 * @package Lucid\Adapter\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FtpDriver extends AbstractFtp implements SupportsVisibility, SupportsPermission
{
    /**
     * connKeys
     *
     * @var array
     */
    protected static $connKeys = [
        'host', 'port', 'user',
        'password', 'ssl', 'passive'
    ];

    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        return (bool)ftp_nlist($this->getConnection(), $path);
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        if (!($stat = $this->statPath($path))) {
            return false;
        }

        return 'file' === $this->getTypeFromString($stat[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        if (!($stat = $this->statPath($path))) {
            return false;
        }

        return 'dir' === $this->getTypeFromString($stat[0]);
    }

    /**
     * {@inheritdoc}
     * FTP does not resolve links. Always report false.
     */
    public function isLink($path)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function writeFile($path, $contents = null)
    {
        $res = $this->writeStream($path, $stream = $this->getTempfile($contents));
        fclose($stream);

        if (false !== $res && ftp_chmod($this->getConnection(), $this->filePermission(), $path)) {
            return $res;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile($path, $contents = null)
    {
        if (false !== ($res = $this->writeStream($path, $stream = $this->getTempfile($contents)))) {
            return $res;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($path, $offset = null, $maxlen = null)
    {
        if (ftp_fget($this->getConnection(), $tmp = $this->getTempfile(), $path, $this->getOption('transfer_mode'))) {
            rewind($tmp);
            return stream_get_contents($tmp, null !== $maxlen ? $maxlen : -1, null !== $offset ? $offset : -1);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $stream, $offset = null, $maxlen = null)
    {
        $bytes = $this->doWriteStream($path, $stream, $offset, $maxlen);

        if (false !== $bytes && false !== ftp_chmod($this->getConnection(), $this->filePermission(), $path)) {
            return $bytes;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $stream, $offset = null, $maxlen = null)
    {
        return $this->doWriteSteam($path, $stream, $offset, $maxlen);
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        if (false === @ftp_fget($this->getConnection(), $stream = tmpfile(), $path, $this->getOption('transfer_mode'))) {
            fclose($stream);

            return false;
        }

        rewind($stream);

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function createDirectory($dir, $recursive = true, $permission = 0755)
    {
        $dirs = explode($this->directorySeparator, $dir);

        if (!$recursive && 1 < count($dirs) && !$this->exists(dirname($dir))) {
            throw new \RuntimeException(
                sprintf('Cannot create directory %s, parent directory does not exist.', $dir)
            );
        }

        $root = $dirs[0];
        $current = '';

        $connection = $this->getConnection();

        do {
            $current = ltrim($current . $this->directorySeparator . array_shift($dirs), $this->directorySeparator);

            if ($this->isDir($current)) {
                continue;
            }

            $this->doCreateDir($connection, $current, $permission);

        } while (0 !== count($dirs));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile($path)
    {
        return ftp_delete($this->getConnection(), $path);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDirectory($path)
    {
        if ($this->doWipeDir($path)) {
            return $this->doRemoveDir($path);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile($file, $target)
    {
        if (!$perm = $this->getPermission($file)) {
            return false;
        }

        return $this->doCopyFile($file, $target, $perm->getMode());
    }


    /**
     * {@inheritdoc}
     */
    public function rename($source, $target)
    {
        if (ftp_rename($this->getConnection(), $source, $target)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission($path, $permission, $recursive = true)
    {
        if (false !== $ret = ftp_chmod($this->getConnection(), $permission, $path)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission($path)
    {
        $stat = $this->statPath($path);
        $res = $this->parseFtpStat($path, $stat[0]);

        return new Permission($res['permission']);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType($path)
    {
        if (FTP_MOREDATA !== $ret = @ftp_nb_fget(
            $conn = $this->getConnection(),
            $tmp = $this->getTempfile(),
            $path,
            $this->getOption('transfer_mode')
        )) {
            fclose($tmp);

            return false;
        }

        if (!$this->getOption('force_detect_mime')) {
            return MimeType::getFromExtension($path);
        }

        $cnt = '';

        // we just want the very first bytes of the file:
        while (FTP_MOREDATA === $ret && is_resource($tmp)) {
            if (ftell($tmp) > 16) {
                $cnt = $this->finishChuckedStream($tmp);
            } else {
                $ret = ftp_nb_continue($conn);
            }
        }

        if (is_resource($tmp)) {
            $cnt = $this->finishChuckedStream($tmp);
        }

        if (MimeType::defaultType() === $mime = MimeType::getFromContent($cnt)) {
            return MimeType::getFromExtension($path);
        }

        return $mime;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo($path)
    {
        $path = (null === $path || 0 === strlen($path)) ? '.' : $path;

        if (!$stat = $this->statPath($path)) {
            return false;
        }

        return $this->createPathInfo(
            $this->statInfoToPathInfo($this->parseFtpStat($path, $stat[0]))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFile($path)
    {
        if ($this->isFile($path)) {
            return true;
        }

        if (!$this->ensureDirectory(dirname($path))) {
            return false;
        }

        return (bool)$this->writeFile($path, null);
    }

    /**
     * {@inheritdoc}
     */
    public function ensureDirectory($dir)
    {
        if ($this->isDir($dir)) {
            return true;
        }

        return $this->createDirectory($dir, true);
    }

    /**
     * {@inheritdoc}
     */
    public function listDirectory($dir, $recursive = true)
    {
        return $this->doListDirectory($dir, (bool)$recursive);
    }

    /**
     * doCopyFile
     *
     * @param mixed $file
     * @param mixed $target
     * @param mixed $perm
     *
     * @return void
     */
    protected function doCopyFile($file, $target, $perm)
    {
        if (!$stream = $this->readStream($file)) {
            return false;
        }

        $bytes = $this->doWriteStream($target, $stream);

        if (false !== $bytes && ftp_chmod($this->getConnection(), $perm, $target)) {
            return $bytes;
        }

        return false;
    }

    /**
     * parseFtpStat
     *
     * @param mixed $path
     * @param mixed $item
     * @param string $base
     *
     * @return PathInfo
     */
    protected function parseFtpStat($path, $item, $base = '')
    {
        list ($pem, $num, $size, $month, $day, $time, $filename) = $this->listStatPath($item);

        // $permission = 0100000 | octdec($this->translatePemString($pem));
        // back conversion to 4 digit octal value: `$permission & 0777`;
        // @see SftpDriver
        $permission = octdec($this->translatePemString($pem));

        $ds   = $this->directorySeparator;
        $type = $this->getTypeFromString($item);

        $size = 'dir' !== $type ? (int)$size : null;
        $path = ltrim($base . $ds . $path, $ds) . $ds . $filename;
        $timestamp = strtotime(implode(' ', [$month, $day, $time]));

        return compact('type', 'path', 'size', 'timestamp', 'permission');
    }

    /**
     * listStatPath
     *
     * @param string $item
     *
     * @return array
     */
    protected function listStatPath($item)
    {
        $parts = explode(' ', preg_replace('~\s+~', ' ', ltrim($item)), 9);

        return [$parts[0], $parts[1], $parts[4], $parts[5], $parts[6], $parts[7], $parts[8]];
    }

    /**
     * finishChuckedStream
     *
     * @param resource $stream
     *
     * @return string
     */
    protected function finishChuckedStream(&$stream)
    {
        rewind($stream);
        $cnt = stream_get_contents($stream);
        fclose($stream);

        return $cnt;
    }

    /**
     * doListDirectory
     *
     * @param string  $dir
     * @param boolean $recursive
     * @param array   $result
     * @param string  $parent
     *
     * @return array
     */
    protected function doListDirectory($dir, $recursive = false, &$result = [], $parent = null)
    {
        $stat = $this->statPath($dir);

        foreach ($stat as $item) {
            $info = $this->statInfoToPathInfo($this->parseFtpStat($dir, $item));
            $bn = basename($info['path']);

            if (in_array($bn, ['.', '..'])) {
                continue;
            }

            $sp = $this->directorySeparator;
            $key = $parent ? $parent . $sp . $bn : $bn;
            $path = 0 === strlen($dir) ? $bn : $dir . $sp . $bn;

            $result[$key] = $this->createPathInfo($info);

            if ('dir' === $info['type'] && $recursive) {
                $prefix =  $parent ? $parent . $sp . $bn : $bn;
                $this->doListDirectory($path, $recursive, $result, $prefix);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function statInfoToPathInfo(array $stat)
    {
        $info = array_intersect_key($stat, array_flip(['type', 'path', 'timestamp']));

        if ('file' === $info['type']) {
            $info['size'] = $stat['size'];
        }

        $this->setInfoPermission($info, $stat['permission']);

        return $info;
    }

    /**
     * Creates a directory on the ftp server.
     *
     * @param resource $connection the ftp resource
     * @param string   $path
     * @param int      $permission permission mode in ocal form
     *
     * @return boolean `FALSE` if failure.
     */
    protected function doCreateDir($connection, $path, $permission)
    {
        if (!ftp_mkdir($connection, $path)) {
            return false;
        }

        return ftp_chmod($connection, $permission, $path);
    }

    /**
     * {@inheritdoc}
     */
    protected function uploadStream($path, $stream)
    {
        if (!ftp_fput($this->getConnection(), $path, $stream, $this->getOption('transfer_mode'))) {
            return false;
        }

        return true;
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
        if (3 > count($stat = ftp_raw($this->getConnection(), 'STAT ' . $path)) || !$stat) {
            return false;
        }

        array_shift($stat);
        array_pop($stat);

        return $stat;
    }

    /**
     * getTypeFromPemStrig
     *
     * @param string $pem
     *
     * @return string
     */
    protected function getTypeFromString($stat)
    {
        return 'd' === substr($stat, 0, 1) ? 'dir' : 'file';
    }

    /**
     * doWriteSteam
     *
     * @param string $path
     * @param resource $stream
     * @param int $offset
     * @param int $maxlen
     *
     * @return int|boolean
     */
    protected function doWriteSteam($path, $stream, $offset, $maxlen)
    {
        $pos = ftell($stream);

        if (null !== $maxlen) {
            $stream = stream_copy_to_stream($stream, $this->getTempfile(), $maxlen);
        }

        fseek($stream, $pos + (null !== $offset ? (int)$offset : 0));

        if (ftp_fput($this->getConnection(), $path, $stream, $this->getOption('transfer_mode'))) {
            fseek($pos);

            return $this->contentSize(stream_get_contents($stream));
        }

        return false;
    }

    /**
     * doRemoveDir
     *
     * @param mixed $path
     *
     * @return void
     */
    protected function doRemoveDir($path)
    {
        foreach ($this->statPath($path) as $file) {

            if ('dir' === $this->getTypeFromString($file)) {
                continue;
            }

            $parts = $this->listStatParts($file);

            $this->deleteFile($path . $this->directorySeparator . $parts[6]);
        }

        return ftp_rmdir($this->getConnection(), $path);
    }

    /**
     * doWipeDir
     *
     * @param mixed $path
     * @param mixed $recursive
     *
     * @return void
     */
    protected function doWipeDir($path)
    {
        foreach ($this->statPath($path) as $file) {

            $parts = $this->listStatParts($file);
            $file = $path . $this->directorySeparator . $parts[6];

            if ('dir' === $this->getTypeFromString($file)) {
                $this->doRemoveDir($file);
                continue;
            }

            if (!$this->deleteFile($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * modstrToInt
     *
     * @param string $mod
     *
     * @return int
     */
    protected function modstrToInt($mod)
    {
        return array_sum(str_split(substr($mod, -3)));
    }

    /**
     * mapPemString
     *
     * @param string $pem
     *
     * @return string 4 character representation of the access permission
     * settings.
     */
    protected function translatePemString($pem)
    {
        $map = ['-' => 0, 'r' => 4, 'w' => 2, 'x' => 1, 's' => 1, 'T' => 0];

        return implode('', (array_map(function ($part) {
            return array_sum(str_split($part));
        }, str_split(strtr(substr($pem, 1), $map), 3))));
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsLink()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function setupConnection(Connection $connection = null, array $creds = [])
    {
        if (null === $connection) {
            $this->connection = new FtpConnection($creds);
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
            'port' => 21,
            'user' => '',
            'password' => '',
            'ssl' => true,
            'passive' => false,
            'transfer_mode' => FTP_BINARY,
        ]);
    }
}
