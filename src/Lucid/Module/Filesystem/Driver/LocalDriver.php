<?php

/*
 * This File is part of the Lucid\Module\Filesystem package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Filesystem\Driver;

use SplFileInfo;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Lucid\Module\Filesystem\PathInfo;
use Lucid\Module\Filesystem\FilesystemInterface;
use Lucid\Module\Filesystem\Exception\IOException;

/**
 * @class LocalDriver
 *
 * @package Lucid\Module\Filesystem
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class LocalDriver extends AbstractDriver implements NativeInterface
{
    /**
     * options
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor.
     *
     * @param string $root
     * @param array $options
     */
    public function __construct($mount = '/', $options = [])
    {
        parent::__construct($mount);
        $this->options = array_merge(static::defaultOptions(), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function pathInfoAsObject($obj)
    {
        $this->options['pathinfo_as_obj'] = (bool)$obj;
    }

    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        return stream_is_local($path = $this->getPrefixed($path)) && is_dir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        return stream_is_local($path = $this->getPrefixed($path)) && is_file($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isLink($path)
    {
        return stream_is_local($path = $this->getPrefixed($path)) && is_link($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isLocal()
    {
        return stream_is_local($this->prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($path)
    {
        return stream_is_local($path = $this->getprefixed($path)) && file_exists($path);
    }

    /**
     * {@inheritdoc}
     */
    public function writeFile($file, $contents = null)
    {
        if (false !== file_put_contents($this->getPrefixed($file), $contents, LOCK_EX)) {
            return $this->getFileStats($file);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile($file, $contents = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($file, $offset = null, $maxlen = null)
    {
        return file_get_contents($this->getPrefixed($file), null, null, $offset ?: null, $maxlen ?: null);
    }

    /**
     * {@inheritdoc}
     */
    public function createDirectory($dir, $permission = 0755, $recursive = true)
    {
        if (false !== @mkdir($this->getprefixed($dir), $permission, (bool)$recursive)) {
            return $this->getFileStats($dir);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function copyFile($file, $target)
    {
        if (false !== $this->doCopyFile($this->getPrefixed($file), $t = $this->getPrefixed($target))) {
            return $this->getFileStats($target);
        }

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTimestamp($path)
    {
        if ($this->exists($path)) {
            return $this->touch($path);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function touch($path, $time = null, $atime = null)
    {
        if (false !== @touch($this->getPrefixed($path, $time, $atime))) {
            return $htis->getFileStats($path);
        }

        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $stream, $maxlen = null, $start = 0)
    {
        if (false !== $this->doWriteStream($this->getPrefixed($path), $stream, $maxlen, $start)) {
            return $this->getFileStats($path);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $stream, $maxlen = null, $start = 0)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($file)
    {
        if (!$stream = fopen($this->getPrefixed($file), 'r')) {
            return false;
        }

        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function setPermission($path, $permission, $recursive = true)
    {
        if (false !== @chmod($this->getPrefixed($path), $permission, (bool)$recursive)) {
            list ($permission, $visibility) = $this->dumpPermission($permission);

            return compact('path', 'visibility', 'permission');
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermission($path)
    {
        if (!file_exists($location = $this->getPrefixed($path))) {
            return false;
        }

        list($permission, $visibility) = $this->pathPermissions($location);

        return compact('path', 'visibility', 'permission');
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile($file)
    {
        if (false !== @unlink($this->getPrefixed($file))) {
            return true;
        }

        throw IOException::rmFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDirectory($path)
    {
        try {
            $this->doRemoveDir($path = $this->getPrefixed($path));
        } catch (\Exception $e) {
            throw IOException::rmDir($path);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function copyDirectory($dir, $target)
    {
        if (!$this->isDir($dir)) {
            throw new IOException(sprintf('"%s" is not a directory', $this->getPrefixed($dir)));
        }

        $flags = FilesystemIterator::SKIP_DOTS;

        if ($this->followSymlinks()) {
            $flags = $flags | FilesystemIterator::FOLLOW_SYMLINKS;
        }

        $bytes = $this->doCopyDir($this->getPrefixed($dir), $this->getPrefixed($target), $flags);

        return $this->getFileStats($target);
    }

    /**
     * {@inheritdoc}
     */
    public function listDirectory($path, $recursive = false)
    {
        $contents = [];

        foreach ($itr = $this->getIterator($this->getPrefixed($path), $recursive) as $path => $info) {
            $key = $recursive ? $itr->getSubPathname() : $info->getBaseName();
            $contents[$key] = $this->fInfoToPathInfo($info);
        }

        return $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function rename($file, $target)
    {
        $path = $target;

        $this->ensureDirectoryExists(dirname($target = $this->getPrefixed($target)));

        if (false !== @rename($$this->getPrefixed($file), $target)) {
            return $this->getFileStats($path);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function ensureDirectory($dir)
    {
        $this->ensureDirectoryExists($this->getPrefixed($dir));

        return $this->getFileStats($dir);
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFile($file)
    {
        $this->ensureFileExists($this->getPrefixed($file));

        return $this->getFileStats($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo($path)
    {
        return $this->fInfoToPathInfo(new SplFileInfo($this->getPrefixed($path)));
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType($path)
    {
        $mimetype = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->getPrefixed($path));

        return compact('path', 'mimetype');
    }

    /**
     * doCopyDir
     *
     * @param string $source
     * @param string $target
     * @param int    $flags
     *
     * @return int bytes copied
     */
    protected function doCopyDir($source, $target, $flags)
    {
        $this->ensureDirectoryExists($target);

        $bytes = 0;
        $sp = $this->directorySeparator;

        foreach (new FilesystemIterator($source, $flags) as $path => $info) {
            $tfile = $target . $sp . $info->getBaseName();

            if ($info->isDir()) {
                $bytes += $this->doCopyDir($path, $tfile, $flags);
            }

            if ($info->isFile()) {
                $bytes += $this->doCopyFile($path, $tfile);
            }
        }

        return $bytes;
    }

    /**
     * doCopyFile
     *
     * @param mixed $file
     * @param mixed $target
     *
     * @return void
     */
    protected function doCopyFile($file, $target)
    {
        if (!$stream = fopen($file, 'r')) {
            return false;
        }

        $bytes = $this->doWriteStream($target, $stream, filesize($file), 0);

        if (!fclose($stream) || false === $bytes) {
            return false;
        }

        return $bytes;
    }

    /**
     * doWriteStream
     *
     * @param mixed $path
     * @param mixed $stream
     * @param mixed $maxlen
     * @param int $start
     *
     * @return int|bool
     */
    protected function doWriteStream($path, $stream, $maxlen = null, $start = 0)
    {
        if (!$handle = fopen($path, 'w')) {
            return false;
        }

        $bytes = stream_copy_to_stream($stream, $handle, $maxlen ?: -1, $start ?: 0);

        if (!fclose($handle)) {
            return false;
        }

        return $bytes;
    }

    /**
     * Removes a directory recursively.
     *
     * @param string $dir
     *
     * @return void
     */
    protected function doRemoveDir($dir)
    {
        $iterator = new FilesystemIterator($dir, FilesystemIterator::CURRENT_AS_SELF|FilesystemIterator::SKIP_DOTS);

        foreach ($iterator as $fileInfo) {

            if ($fileInfo->isFile() || $fileInfo->isLink()) {
                unlink($fileInfo->getPathName());
                continue;
            }

            if ($fileInfo->isDir()) {
                $this->doRemoveDir($dir = $fileInfo->getPathName());
                continue;
            }
        }

        rmdir($dir);
    }


    /**
     * ensureDirectoryExists
     *
     * @param string $dir
     *
     * @return string
     */
    protected function ensureDirectoryExists($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, $this->directoryPermissions(), true);
        }

        return realpath($dir);
    }

    /**
     * ensureFileExists
     *
     * @param string $file
     *
     * @return string
     */
    protected function ensureFileExists($file)
    {
        if (!is_file($file)) {
            $this->ensureDirectoryExists(dirname($file));
            touch($file);
        }

        return realpath($file);
    }


    /**
     * getFileStats
     *
     * @param string $path
     *
     * @return array
     */
    protected function getFileStats($path, $isPrefixed = false)
    {
        $loc = $isPrefixed ? $path : $this->getPrefixed($path);

        list ($permission, $visibility) = $this->pathPermissions($loc);

        return compact('path', 'visibility', 'permission');
    }

    protected function pathPermissions($path)
    {
        return $this->dumpPermission(octdec(substr(sprintf('%o', fileperms($path)), -4)));
    }

    /**
     * dumpPermission
     *
     * @param int $mod
     *
     * @return array
     */
    protected function dumpPermission($mod)
    {
        return ['0'.decoct($mod), $this->getVisibilityFromMod($mod)];
    }

    /**
     * directoryPermissions
     *
     * @return void
     */
    protected function directoryPermissions()
    {
        return $this->options['directory_permission'];
    }

    /**
     * followSymlinks
     *
     * @return boolean
     */
    protected function followSymlinks()
    {
        return $this->options['follow_symlinks'];
    }

    /**
     * getIterator
     *
     * @param mixed $path
     * @param mixed $recursive
     *
     * @return FilesystemIterator
     */
    protected function getIterator($path, $recursive)
    {
        $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::SKIP_DOTS;

        if ($this->followSymlinks()) {
            $flags = $flags | FilesystemIterator::FOLLOW_SYMLINKS;
        }

        if ($recursive) {
            $itr = new RecursiveDirectoryIterator(
                $path,
                $flags | FilesystemIterator::CURRENT_AS_FILEINFO
            );

            return new RecursiveIteratorIterator($itr);
        }

        return new FilesystemIterator($path, $flags);
    }

    /**
     * fInfoToPathInfo
     *
     * @param \SplFileInfo $file
     *
     * @return void
     */
    protected function fInfoToPathInfo(\SplFileInfo $file)
    {
        $info['type']      = $file->getType();
        $info['path']      = $this->getUnprefixed($file->getPathName());
        $info['timestamp'] = $file->getMTime();

        if ('file' === $info['type']) {
            $info['size'] = $file->getSize();
        }

        return $this->createPathInfo($info);
    }

    /**
     * {@inheritdoc}
     */
    protected function pathInfoReturnsArray()
    {
        return !$this->options['pathinfo_as_obj'];
    }

    protected static function defaultOptions()
    {
        return [
            'directory_permission' => 0755,
            'follow_symlinks' => false,
            'pathinfo_as_obj' => false,
        ];
    }
}
