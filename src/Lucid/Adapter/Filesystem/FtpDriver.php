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

use Lucid\Filesystem\PathInfo;
use Lucid\Filesystem\Mime\MimeType;
use Lucid\Filesystem\Permission;
use Lucid\Filesystem\FilesystemInterface;
use Lucid\Filesystem\Driver\SupportsVisibility;
use Lucid\Filesystem\Driver\SupportsPermission;
use Lucid\Adapter\Filesystem\Ftp\Connection as FtpConnection;
use Lucid\Adapter\Filesystem\FtpConnectionInterface as Connection;
use Lucid\Adapter\Filesystem\Traits\StatCacheTrait;

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
    use StatCacheTrait;

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
        if (!($stat = $this->getStat($path))) {
            return false;
        }

        return 'file' === $this->getTypeFromString($stat[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        if (!($stat = $this->getStat($path))) {
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
        if (false !== @ftp_fget($this->getConnection(), $tmp = $this->getTempfile(), $path, $this->getOption('transfer_mode'))) {
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
    public function createDirectory($dir, $permission = null, $recursive = false)
    {
        if ($this->hasStat($dir)) {
            return false;
        }

        $dirs = explode($this->directorySeparator, $dir);

        if (!$recursive && 1 < count($dirs) && !$this->exists(dirname($dir))) {
            return false;
        }

        $root = $dirs[0];
        $current = '';
        $permission = $permission ?: $this->directoryPermission();

        $connection = $this->getConnection();

        do {
            $current = ltrim($current . $this->directorySeparator . array_shift($dirs), $this->directorySeparator);

            if ($this->isDir($current)) {
                continue;
            }

            if ($this->doCreateDir($connection, $current, $permission)) {
                continue;
            }

            return false;

        } while (0 !== count($dirs));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile($path)
    {
        if (@ftp_delete($this->getConnection(), $path)) {
            $this->clearStat($path);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDirectory($path)
    {
        if ($this->doRemoveDir($path)) {
            $this->clearStat($path);

            return true;
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
            $this->reStat($source, $target);

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
        $stat = $this->getStat($path);
        $res = $this->parseFtpStat($stat[0]);

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

        if (!$stat = $this->getStat($path)) {
            return false;
        }

        $info = $this->statInfoToPathInfo($this->parseFtpStat($stat[0]));

        if ('.' === $info['path']) {
            $info['path'] = $path;
        }

        return $this->createPathInfo($info);
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

        return $this->createDirectory($dir, null, true);
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
    protected function parseFtpStat($item, $base = '')
    {
        list ($pem, $num, $size, $month, $day, $time, $filename) = $this->listStatPath($item);

        $ds   = $this->directorySeparator;

        // $permission = 0100000 | octdec($this->translatePemString($pem));
        // back conversion to 4 digit octal value: `$permission & 0777`;
        // @see SftpDriver
        $permission = octdec($this->translatePemString($pem));
        $type = $this->getTypeFromString($item);
        $path = trim($base.$ds.$filename, $ds);
        $timestamp = strtotime(implode(' ', [$month, $day, $time]));

        if (PathInfo::T_DIRECTORY === $type) {
            return compact('type', 'path', 'timestamp', 'permission');
        }

        $size = (int)$size;

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

        if (count($parts) < 9) {
            return false;
        }

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
     * getDirectoryListing
     *
     * @param mixed $path
     * @param mixed $recursive
     *
     * @return void
     */
    protected function getDirectoryListing($path, $recursive = false)
    {
        $list = ftp_rawlist($this->getConnection(), trim('-lna '.preg_replace('#(\s)+#', "\ ", $path)), $recursive);

        $items = [];

        $listing = array_filter($list, function ($item) {
            if (empty($item) || preg_match('#(\s\.\.?$|^total)#', $item)) {
                return false;
            }

            return true;
        });

        $basePath = trim($path, '.');
        while ($item = array_shift($listing)) {
            if (preg_match('#.*\:$#', $item)) {
                $basePath = trim($item, './:');
                continue;
            }
            $items[] = $this->parseFtpStat($item, $basePath);
        }

        return $items;
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
        $ds = $this->directorySeparator;
        $list = $this->getDirectoryListing($dir, $recursive);

        foreach ($list as $info) {
            $key= 0 === strpos($info['path'], $dir.$ds) ? substr($info['path'], strlen($dir.$ds)) : $info['path'];
            $result[$key] = $this->createPathInfo($info);
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
        if (false === @ftp_mkdir($connection, $path)) {
            return false;
        }

        $this->clearStat($pn = dirname($path));

        return @ftp_chmod($connection, $permission, $path);
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
        if (0 === count($stat = ftp_rawlist($this->getConnection(), preg_replace('#(\s)+#', "\ ", $path), false))
            || !$stat
        ) {
            return false;
        }

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
        if ($this->doWipeDir($path)) {
            return ftp_rmdir($this->getConnection(), $path);
        }

        return false;
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
        if (false === $stat = $this->getStat($path)) {
            return false;
        }

        foreach ($stat as $file) {

            $parts = $this->listStatPath($file);

            if (in_array($parts[6], ['.', '..'])) {
                continue;
            }

            $file = $path . $this->directorySeparator . $parts[6];

            if ('dir' === $type = $this->getTypeFromString($parts[0])) {

                if (!$this->doRemoveDir($file)) {
                    return false;
                }

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
