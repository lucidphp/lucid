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
     * Constructor.
     *
     * @param string $mount
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
    public function writeFile($path, $contents = null)
    {
        $loc = $this->getPrefixed($path);
        if (false === $size = $this->writeContents($loc, $contents, $perm = $this->filePermission())) {
            return false;
        }

        $type = 'file';
        $mimetype = $this->doGetMimeType($loc);

        list ($permission, $visibility) = $this->dumpPermission($perm);

        return compact('type', 'path', 'size', 'visibility', 'permission', 'mimetype', 'contents');
    }

    /**
     * {@inheritdoc}
     */
    public function updateFile($path, $contents = null)
    {
        if (false === $size = $this->writeContents($this->getPrefixed($path), $contents)) {
            return false;
        }

        return compact('type', 'path', 'size', 'contents');
    }

    /**
     * {@inheritdoc}
     */
    public function readFile($path, $offset = null, $maxlen = null)
    {
        $file = $this->getPrefixed($path);

        $args = [$file, false, null, $offset ?: -1];

        if (null !== $maxlen) {
            $args[] = (int)$maxlen;
        }

        if (false !== ($contents = @call_user_func_array('file_get_contents', $args))) {
            return compact('path', 'contents');
        }

        return false;
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

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTimestamp($path, $time)
    {
        if ($this->exists($path)) {
            return $this->touch($path, $time);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function touch($path, $time = null, $atime = null)
    {
        if (false !== @touch($this->getPrefixed($path, $time, $atime))) {
            return $this->getFileStats($path);
        }

        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $stream, $maxlen = null, $start = 0)
    {
        if (false !== $this->doWriteStream($loc = $this->getPrefixed($path), $stream, $maxlen, $start)) {
            return $this->getFileStats($path);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $stream, $maxlen = null, $start = 0)
    {
        return $this->writeStream($path, $stream, $maxlen, $start);
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
            $this->doWipeDir($path = $this->getPrefixed($path));
            rmdir($path);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function copyDirectory($dir, $target)
    {
        if (!$this->isDir($dir)) {
            return false;
        }

        $flags = FilesystemIterator::SKIP_DOTS;

        if ($this->followSymlinks()) {
            $flags = $flags | FilesystemIterator::FOLLOW_SYMLINKS;
        }

        $bytes = $this->doCopyDir($this->getPrefixed($dir), $this->getPrefixed($target), $flags);

        $path = $target;

        return compact('path');
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

        if (false !== @rename($this->getPrefixed($file), $target)) {
            return compact('path');
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function ensureDirectory($path)
    {
        $this->ensureDirectoryExists($this->getPrefixed($path));

        return compact('path');
    }

    /**
     * {@inheritdoc}
     */
    public function ensureFile($path)
    {
        $this->ensureFileExists($this->getPrefixed($path));

        return compact('path');
    }

    /**
     * {@inheritdoc}
     */
    public function getPathInfo($path)
    {
        return $this->createPathInfo(
            $this->fInfoToPathInfo(new SplFileInfo($this->getPrefixed($path)))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMimeType($path)
    {
        $mimetype = $this->doGetMimeType($this->getPrefixed($path));

        return compact('path', 'mimetype');
    }

    protected function doGetMimeType($path)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
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
        if (!$stream = @fopen($file, 'r')) {
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
    protected function doWipeDir($dir)
    {
        $iterator = new FilesystemIterator($dir, FilesystemIterator::CURRENT_AS_SELF|FilesystemIterator::SKIP_DOTS);

        foreach ($iterator as $fileInfo) {

            if ($fileInfo->isFile() || $fileInfo->isLink()) {
                unlink($fileInfo->getPathName());
                continue;
            }

            if ($fileInfo->isDir()) {
                $this->doWipeDir($dir = $fileInfo->getPathName());
                rmdir($dir);
                continue;
            }
        }

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
     * Writes content to a file.
     *
     * @param string $file
     * @param string $contents
     * @param int    $perm
     *
     * @return void
     */
    protected function writeContents($file, $contents, $perm = null)
    {
        if (false === ($size = file_put_contents($file, $contents, LOCK_EX))) {
            return false;
        }

        if (null !== $perm) {
            chmod($file, $perm);
        }

        return $size;
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
            $flags = $flags | FilesystemIterator::FOLLOW_SYMLINKS | FilesystemIterator::UNIX_PATHS;
        }

        if ($recursive) {
            $itr = new RecursiveDirectoryIterator(
                $path,
                $flags | FilesystemIterator::CURRENT_AS_FILEINFO
            );

            return new RecursiveIteratorIterator($itr, RecursiveIteratorIterator::SELF_FIRST);
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
        $type = $file->getType();

        $info['type']      = $file->getType();
        $info['path']      = $this->getUnprefixed($file->getPathname());
        $info['timestamp'] = $file->getMTime();

        if ('file' === $info['type']) {
            $info['size'] = $file->getSize();
        }

        return $info;
    }

    protected function filePermission()
    {
        return $this->options['file_permission'];
    }

    protected static function defaultOptions()
    {
        return [
            'directory_permission' => 0755,
            'file_permission' => 0664,
            'follow_symlinks' => false,
            'pathinfo_as_obj' => false,
        ];
    }
}
