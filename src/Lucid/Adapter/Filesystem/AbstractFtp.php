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

use Lucid\Filesystem\FilesystemInterface;
use Lucid\Filesystem\Driver\AbstractDriver;
use Lucid\Adapter\Filesystem\FtpConnectionInterface as Connection;

/**
 * @class AbstractFtp
 *
 * @package Lucid\Adapter\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractFtp extends AbstractDriver
{
    protected $options;
    protected $connection;
    protected static $connKeys = [];

    /**
     * Constructor.
     *
     * @param string $mount
     * @param array $options
     * @param Connection $conn
     *
     * @return void
     */
    public function __construct($mount = null, array $options = [], Connection $conn = null)
    {
        $this->setConnectionAndOptions($conn, $options);
        $this->setPrefix($mount);
    }

    /**
     * {@inheritdoc}
     */
    public function copyDirectory($dir, $target)
    {
        if ($this->exists($target)) {
            return false;
        }

        // make sure to return fileperms as integer an not as string while
        // listing:
        $opts = $this->getOption('permission_as_string');
        $opts = $this->setOption('permission_as_string', false);
        $ret = $this->doCopyDirectory($dir, $target);
        $opts = $this->setOption('permission_as_string', $opts);

        return $ret;
    }

    /**
     * setOptions
     *
     * @param array $options
     *
     * @return void
     */
    protected function setConnectionAndOptions(Connection $conn = null, array $options = [])
    {
        $options = array_merge(static::defaultOptions(), $options);
        $keys = array_flip(static::$connKeys);
        $this->setupConnection($conn, array_intersect_key($options, $keys));

        $this->options = array_diff_key($options, $keys = array_flip(static::$connKeys));
    }

    /**
     * Gets an FTP Buffer or creates a new one..
     *
     * @return resource the FTP buffer.
     */
    protected function getConnection()
    {
        if (!$this->connection->isConnected()) {
            $this->connection->connect();
            $this->connection->setMountPoint($this->prefix);
        }

        return $this->connection->getConnection();
    }

    /**
     * uploadStream
     *
     * @param string $path
     * @param resource $stream
     *
     * @return boolean
     */
    abstract protected function uploadStream($path, $stream);

    /**
     * doWriteStream
     *
     * @param string $path
     * @param resource $stream
     * @param int $maxlen
     * @param int $start
     *
     * @return int|boolean
     */
    protected function doWriteStream($path, $stream, $offset = null, $maxlen = null)
    {
        $pos = ftell($stream);

        if (null !== $maxlen) {
            $maxlen = (int)$offset + (int)$maxlen;
            stream_copy_to_stream($stream, $tmp = $this->getTempfile(), $maxlen);
            $stream = $tmp;
        }

        fseek($stream, $pos = $pos + (null !== $offset ? (int)$offset : 0));

        if ($this->uploadStream($path, $stream)) {
            fseek($stream, $pos);

            return $this->contentSize(stream_get_contents($stream));
        }

        return false;
    }

    /**
     * doCopyFile
     *
     * @param mixed $file
     * @param mixed $target
     * @param mixed $perm
     *
     * @return int
     */
    abstract protected function doCopyFile($file, $target, $perm);

    /**
     * supportsLink
     *
     *
     * @return boolean
     */
    abstract protected function supportsLink();

    /**
     * doCopyDirectory
     *
     * @param mixed $dir
     * @param mixed $target
     *
     * @return void
     */
    protected function doCopyDirectory($dir, $target, $mode = null)
    {
        if (null === $mode) {
            if (false === $perm = $this->getPermission($dir)) {
                return false;
            }

            $mode = $perm->getMode();
        }

        if (!$this->createDirectory($target, $mode, false)) {
            return false;
        }

        if (!$list = $this->listDirectory($dir)) {
            return false;
        }

        $bytes = 0;
        $sp = $this->directorySeparator;

        foreach ($list as $relPath => $object) {

            $basename = basename($object['path']);
            $pName = $object['path'];
            $tName = $target.$sp.$basename;

            if ('link' === $object['type'] && $this->supportsLink()) {
                //@TODO add support for copy links
                continue;
            }

            if ('file' === $object['type']) {
                if (false !== ($ret = $this->doCopyFile($pName, $tName, $object['permission']))) {
                    $bytes += $ret;
                    continue;
                }
            }

            if ('dir' === $object['type']) {
                if (false !== $ret = $this->doCopyDirectory($pName, $tName, $object['permission'])) {
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
    protected function supportsPemMod()
    {
        return true;
    }

    /**
     * setPassive
     *
     * @param mixed $passive
     *
     * @return void
     */
    public function setPassive($passive)
    {
        $this->options['passive'] = (bool)$passive;
    }

    /**
     * setSecure
     *
     * @param mixed $ssl
     *
     * @return void
     */
    public function setSecure($ssl)
    {
        $this->options['ssl'] = (bool)$ssl;
    }

    /**
     * setSecure
     *
     * @param mixed $ssl
     *
     * @return void
     */
    public function setTransferMode($mode)
    {
        if (!in_array($mode, [FTP_ASCII, FTP_BINARY])) {
            throw new \InvalidArgumentException(sprintf('Invalid transfer mode %s', (string)$mode));
        }

        $this->options['transfer_mode'] = $mode;
    }

    /**
     * Create a temporary file handle.
     *
     * @param string $contents
     *
     * @return resource
     */
    protected function &getTempfile(&$contents = null)
    {
        $tmp = tmpfile();

        if (null !== $contents) {
            fwrite($tmp, $contents);
            rewind($tmp);
        }

        return $tmp;
    }

    /**
     * setupConnection
     *
     * @param Connection $connection
     *
     * @return void
     */
    abstract protected function setupConnection(Connection $connection = null, array $options = []);
}
