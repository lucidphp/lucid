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

use Lucid\Module\Filesystem\FilesystemInterface;
use Lucid\Module\Filesystem\Driver\AbstractDriver;
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

    public function __construct($mount = null, array $options = [], Connection $conn = null)
    {
        $this->options = array_merge(static::defaultOptions(), $options);
        $this->setPrefix($mount);

        $this->setupConnection($conn);
    }

    /**
     * doWriteStream
     *
     * @param mixed $path
     * @param mixed $stream
     * @param mixed $maxlen
     * @param int $start
     *
     * @return int|boolean
     */
    protected function doWriteStream($path, $stream, $maxlen = null, $start = 0)
    {
        if (null !== $maxlen) {
            $tmp = tmpfile();
            $stream = stream_copy_to_stream($stream, $tmp, $maxlen);
        }

        if (null !== $offset) {
            fseek($stream, ftell($stream) + (int)$offset);
        }

        $pos = ftell($stream);

        if (!$this->uploadStream($path, $stream)) {
            return false;
        }

        if (0 === $pos) {
            rewind($stream);
        } else {
            fseek($stream, $pos);
        }

        return mb_strlen(stream_get_contents($stream));
    }

    abstract protected function uploadStream($path, $stream);

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
     * normalizeResult
     *
     * @param mixed $path
     * @param mixed $item
     * @param string $base
     *
     * @return PathInfo
     */
    protected function normalizeResult($path, $item, $base = '')
    {
        list ($pem, $num, $size, $month, $day, $time,) = $this->listStatPath($item);

        $visibility = $this->getVisibilityFromMod($permission = $this->translatePemString($pem));

        $type = $this->getTypeFromPemStrig($pem);
        $size = 'dir' !== $type ? (int)$size : null;
        $path = $base . $this->directorySeparator . $path;
        $timestamp = strtotime(implode(' ', [$month, $day, $time]));

        return compact('type', 'path', 'size', 'timestamp', 'visibility', 'permission');
    }

    /**
     * listStatPath
     *
     * @param mixed $item
     *
     * @return void
     */
    protected function listStatPath($item)
    {
        $parts = explode(' ', preg_replace('~\s+~', ' ', ltrim($item)), 9);

        return [$parts[0], $parts[1], $parts[4], $parts[5], $parts[6], $parts[7], $parts[8]];
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
        $map = ['-' => 0, 'r' => 4, 'w' => 2, 'x' => 1];

        return '0' . implode('', (array_map(function ($part) {
            return array_sum(str_split($part));
        }, str_split(strtr(substr($pem, 1), $map), 3))));
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
    abstract protected function setupConnection(Connection $connection = null);
}
