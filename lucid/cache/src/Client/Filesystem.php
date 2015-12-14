<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Cache\Client;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use Lucid\Cache\CacheInterface;

/**
 * @class FilesystemClient
 * @see AbstractClient
 *
 * @package Selene\Module\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Filesystem extends AbstractClient
{
    /** @var string */
    private $cachedir;

    /**
     * Constructor.
     *
     * @param FilesystemInterface $fs
     * @param string $location
     */
    public function __construct($path)
    {
        $this->setCacheDir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        if (!is_file($file = $this->getFilePath($key))) {
            return false;
        }

        return time() < filemtime($file);
    }

    /**
     * {@inheritdoc}
     */
    public function read($key)
    {
        if (!$this->exists($key)) {
            return;
        }

        list ($data,) = $this->getFileContent($this->getFilePath($key));

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function write($key, $data, $expires = 60, $compressed = false)
    {
        if (CacheInterface::PERSIST === $expires) {
            $expires = strtotime('2037-12-31');
        }

        list($contents, $timestamp) = $this->serializeData($data, $compressed, (int)$expires);

        if (!file_exists($parent = dirname($file = $this->getFilePath($key)))) {
            mkdir($parent);
        }

        if (false === file_put_contents($file, $contents, LOCK_EX)) {
            return false;
        }

        touch($file, $timestamp);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function saveForever($key, $data, $compressed = false)
    {
        return $this->write($key, $data, CacheInterface::PERSIST === $expires, $compressed);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        if (false !== @unlink($this->getFilePath($key))) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        if (!is_dir($this->cachedir)) {
            return false;
        }

        $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO |
            FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;

        $this->rmDir($this->cachedir, $flags);

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function parseExpireTime($expires)
    {
        return $this->expiryToUnixTimestamp($expires);
    }

    /**
     * {@inheritdoc}
     */
    protected function incrementValue($key, $value)
    {
        return $this->setIncrementValue($key, (int)$value);
    }

    /**
     * {@inheritdoc}
     */
    protected function decrementValue($key, $value)
    {
        return $this->setIncrementValue($key, 0 - (int)$value);
    }

    /**
     * setIncrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return boolean
     */
    private function setIncrementValue($key, $value)
    {
        if (!$this->exists($key)) {
            return false;
        }

        list($data, $state) = $this->getFileContent($file = $this->getFilePath($key));

        $timestamp = filemtime($file);

        list($contents,) = $this->serializeData($ret = ((int)$data + $value), static::C_UNCOMPRESSED === $state);

        file_put_contents($file, $contents, LOCK_EX);

        touch($file, $timestamp);

        return $ret;
    }

    /**
     * getFileContent
     *
     * @param mixed $file
     *
     * @access protected
     * @return mixed
     */
    protected function getFileContent($file)
    {
        $state = (int)substr($contents = file_get_contents($file), 0, 1);
        $data = substr($contents, 2);

        $data = static::C_UNCOMPRESSED === $state ? $data = unserialize($data) : unserialize($this->uncompressData($data));

        return [$data, $state];
    }

    /**
     * {@inheritdoc}
     */
    protected function getPersistLevel()
    {
        return CacheInterface::PERSIST;
    }

    /**
     * Returns a base64 encoded comperessed string
     *
     * @param string $data
     *
     * @return string
     */
    private function compressData($data)
    {
        return base64_encode(gzcompress($data));
    }

    /**
     * uncompressData
     *
     * @param string $data
     *
     * @return string
     */
    private function uncompressData($data)
    {
        return gzuncompress(base64_decode($data));
    }

    /**
     * getFilePath
     *
     * @param mixed $key
     *
     * @access private
     * @return string
     */
    private function getFilePath($key)
    {
        $hash = hash('md5', $key);

        return $this->cachedir.DIRECTORY_SEPARATOR.substr($hash, 0, 4).DIRECTORY_SEPARATOR.substr($hash, 4, 20);
    }

    /**
     * serializeData
     *
     * @param mixed $data
     * @param boolean $compressed
     * @param int $timestamp
     *
     * @return array
     */
    private function serializeData($data, $compressed = self::C_UNCOMPRESSED, $timestamp = 0)
    {
        $data = serialize($data);
        $data = $compressed ? $this->compressData($data) : $data;
        $contents = sprintf('%d;%s', $compressed ? static::C_COMPRESSED : self::C_UNCOMPRESSED, $data);

        return [$contents, $timestamp];
    }

    /**
     * setCacheDir
     *
     * @param string $path
     *
     * @return void
     */
    private function setCacheDir($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0775 & umask(), true);
        }

        $this->cachedir = $path;
    }

    /**
     * wipes a directory.
     *
     * @param string $rpath
     * @param int $flags
     *
     * @return void
     */
    private function rmDir($rpath, $flags)
    {
        $itr = new Filesystemiterator($rpath, $flags);

        foreach ($itr as $path => $info) {
            if ($info->isFile()) {
                unlink($path);
            }

            if ($info->isDir()) {
                $this->rmDir($path, $flags);
                rmdir($path);
            }
        }
    }
}
