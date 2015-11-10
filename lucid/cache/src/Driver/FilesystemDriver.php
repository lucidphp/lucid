<?php

/*
 * This File is part of the Lucid\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Lucid\Cache\Driver;

use Lucid\Cache\CacheInterface;

/**
 * @class FilesystemDriver
 * @see AbstractDriver
 *
 * @package Selene\Module\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilesystemDriver extends AbstractDriver
{
    /**
     * cache directory
     *
     * @var Stream\FSDirectory
     * @access protected
     */
    protected $cachedir;

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

        if (!file_put_contents($file = $this->getFilePath($key), $contents, LOCK_EX)) {
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
        foreach ($itr = new \DirectoryIterator($this->cachedir) as $path) {
            if ($itr->isFile()) {
                unlink($itr->getPathname());
            }
        }
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

        list($contents,) = $this->serializeData((int)$data + $value, static::C_UNCOMPRESSED === $state);

        file_put_contents($file, $contents, LOCK_EX);

        touch($file, $timestamp);

        return true;
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

        $data = static::C_UNCOMPRESSED === $state ?
            $data = unserialize($data) :
            unserialize($this->uncompressData($data));

        return [$data, $state];
    }

    /**
     * compressData
     *
     * @param Mixed $data
     * @access private
     * @return String base64 string representation of gzip compressed input
     * data
     */
    private function compressData($data)
    {
        return base64_encode(gzcompress($data));
    }

    /**
     * uncompressData
     *
     * @param Mixed $data
     * @access private
     * @return String Mixed contents of the cached item
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

        return substr($hash, 0, 4).DIRECTORY_SEPARATOR.substr($hash, 4, 20);
    }

    /**
     * serializeWithTime
     *
     * @param Mixed $data
     * @param Mixed $time
     * @param Mixed $compressed
     * @access private
     * @return Mixed file contents
     */
    private function serializeData($data, $compressed = false, $timestamp = 0)
    {
        $data = serialize($data);
        $data = $compressed ? $this->compressData($data) : $data;
        $contents = sprintf('%d;%s', $compressed ? static::C_COMPRESSED : self::C_UNCOMPRESSED, $data);

        return [$contents, $timestamp];
    }

    private function setCacheDir($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0775 & umask(), true);
        }

        $this->cachedir = $path;
    }

    /**
     * {@inheritdoc}
     */
    protected function getPersistLevel()
    {
        return CacheInterface::PERSIST;
    }
}
