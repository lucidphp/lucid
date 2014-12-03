<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Handler;

use InvalidArgumentException;

/**
 * @class FilesystemSessionHandler
 * @see AbstractSessionHandler
 *
 * @package Lucid\Module\Http\Session\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilesystemSessionHandler extends AbstractSessionHandler
{
    private $savePath;

    /**
     * Constructor
     *
     * @param FilesystemInterface $fs
     *
     * @return void
     */
    public function __construct($savePath, $prefix = self::DEFAULT_PREFIX)
    {
        $this->setSavePath($savePath);
        parent::__construct(null, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        return file_exists($file = $this->getFile($sessionId)) ?
            gzuncompress(file_get_contents($file)) :
            '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        file_put_contents($file = $this->getFile($sessionId), gzcompress($data), LOCK_EX);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        if (is_file($file = $this->getFile($sessionId))) {
            return @unlink($file);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        foreach (glob($this->getFile('*')) as $file) {
            if (filemtime($file) <= $maxlifetime) {
                unlink($file);
            }
        }
    }

    /**
     * getFile
     *
     * @param string $sessionId
     *
     * @return string
     */
    protected function getFile($sessionId)
    {
        return ltrim($this->savePath, '\\/') . DIRECTORY_SEPARATOR . $this->getPrefixed($sessionId);
    }

    /**
     * setSavePath
     *
     * @param string $path
     *
     * @return void
     */
    protected function setSavePath($path)
    {
        if (!file_exists($path)) {
            if (false === @mkdir($path, true, 0755 & ~umask())) {
                throw new InvalidArgumentException;
            }
        }

        $this->savePath = $path;
    }
}
