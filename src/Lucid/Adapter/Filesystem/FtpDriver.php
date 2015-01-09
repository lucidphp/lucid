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
use Lucid\Module\Filesystem\FilesystemInterface;
use Lucid\Module\Filesystem\Traits\StreamHelperTrait;
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
    use StreamHelperTrait;
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

        return 'file' === $this->getTypeFromStrig($stat[0]);
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        if (!($stat = $this->statPath($path))) {
            return false;
        }

        return 'dir' === $this->getTypeFromStrig($stat[0]);
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
     * getPathInfo
     *
     * @param mixed $path
     *
     * @return void
     */
    public function getPathInfo($path)
    {
        $path = (null === $path || 0 === strlen($path)) ? '.' : $path;

        if ($stat = $this->statPath($path)) {
            return $this->normalizeResult($path, $stat[0]);
        }
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
        $byte = $this->doWriteStream($path, $stream, $offset, $maxlen);

        if (false !== $bytes && false !== ftp_chmod($this->getConnection(), $path, $this->filePermission())) {
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
        if (!ftp_fget($this->getConnection(), $stream = tmpfile(), $path, $this->getOption('transfer_mode'))) {
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
    public function deleteDirectory($path, $recursive = true)
    {
        if ($recursive) {
            $this->doWipeDir($path);
        }

        $this->doRemoveDir($path);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile($file, $target)
    {
        if (!$stream = $this->readStream($file)) {
            return false;
        }

        if (!$perm = $this->getPermission($file)) {
            return false;
        }

        $bytes = $this->doWriteStream($target, $stream);

        if (false !== $bytes && ftp_chmod($this->getConnection(), $target, $perm['permission'])) {
            return true;
        }

        return false;
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
        if (false !== ftp_chmod($this->getConnection(), $permission, $path)) {
            true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission($path)
    {
        if ($meta = $this->getMetaData($path)) {
            return ['permission' => $meta['permission'], 'visibility' => $meta['visibility']];
        }
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
                $cnt = $this->finisChuckedStream($tmp);
            } else {
                $ret = ftp_nb_continue($conn);
            }
        }

        if (is_resource($tmp)) {
            $cnt = $this->finisChuckedStream($tmp);
        }

        if (MimeType::defaultType() === $mime = MimeType::getFromContent($cnt)) {
            return MimeType::getFromExtension($path);
        }

        return $mime;
    }

    /**
     * finisChuckedStream
     *
     * @param resource $stream
     *
     * @return string
     */
    protected function finisChuckedStream(&$stream)
    {
        rewind($stream);
        $cnt = stream_get_contents($stream);
        fclose($stream);

        return $cnt;
    }

    public function ensureFile($path)
    {
    }

    public function ensureDirectory($dir)
    {
    }

    public function listDirectory($dir, $recursive = true)
    {
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
     * login
     *
     * @param mixed $connection
     *
     * @return void
     */
    protected function login($connection)
    {
        if (!@ftp_login($connection, $u = $this->options['user'], $pw = $this->options['password'])) {
            throw new \RuntimeException(
                sprintf('Could not log in user %s using password: %s.', $u, 0 !== strlen($pw) ? 'yes' : 'no')
            );
        }
    }

    /**
     * setPassiveMode
     *
     * @param resource $connection
     * @param boolean $mode
     *
     * @return void
     */
    protected function setPassiveMode($connection, $mode)
    {
        if ($mode === ftp_pasv($connection, $mode)) {
            return $mode;
        }

        throw new \RuntimeException(
            sprintf('Could not set passive mode for %s:%s.', $this->options['host'], $this->options['port'])
        );
    }

    /**
     * setFtpMount
     *
     * @param resource $connection
     * @param string|null $mount
     *
     * @return void
     */
    protected function setFtpMount($connection, $mount = null)
    {
        if (null === $mount) {
            return;
        }

        if (false === @ftp_chdir($connection, $mount)) {
            ftp_close($connection);
            throw new \RuntimeException(sprintf('Could mount ftp driver to "%s".', $mount));
        }
    }

    /**
     * getTypeFromPemStrig
     *
     * @param string $pem
     *
     * @return string
     */
    protected function getTypeFromStrig($pem)
    {
        return 'd' === substr($pem, 1) ? 'dir' : 'file';
    }

    /**
     * getListForPath
     *
     * @param mixed $path
     *
     * @return array
     */
    protected function getListForPath($path)
    {
        if (false === ($list = @ftp_rawlist($this->getConnection(), '-lna ' . $path))) {
            return [];
        }

        return $list;
    }

    protected function getDirPermissionMode($mode)
    {
        return $this->options['permission_dir'][$mode];
    }

    protected function getFilePermissionMode($mode)
    {
        return $this->options['permission_file'][$mode];
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

            $this->deleteFile($path . '/'. $parts[6]);
        }

        ftp_rmdir($this->getConnection(), $path);
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
            $file = $path . '/' . $parts[6];

            if ('dir' === $this->getTypeFromString($file)) {
                $this->doRemoveDir($file);
                continue;
            }

            $this->deleteFile($file);
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
            'port' => 21,
            'user' => '',
            'password' => '',
            'ssl' => true,
            'passive' => false,
            'transfer_mode' => FTP_BINARY,
            'mount' => '',
            'force_detect_mime' => false
        ];
    }

    protected function setupConnection(Connection $connection = null)
    {
        if (null === $connection) {
            $creds = array_intersect_key(
                $this->options,
                array_flip(['host', 'port', 'user', 'password', 'ssl', 'passive'])
            );

            $this->connection = new FtpConnection($creds);
        } else {
            $this->connection = $connection;
        }
    }
}
