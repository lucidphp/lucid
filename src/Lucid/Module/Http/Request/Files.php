<?php

/*
 * This File is part of the Lucid\Module\Http\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Request;

use Lucid\Module\Common\Helper\Arr;

/**
 * @class Files
 *
 * @package Lucid\Module\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Files implements UploadedFilesInterface
{
    private $files;
    private $fixed;
    private static $fileKeys = ['error', 'name', 'size', 'tmp_name', 'type'];

    public function __construct(array $files = [])
    {
        $this->setFilesArray($files);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilesArray(array $files)
    {
        $this->fixed = null;
        $this->files = $files;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesArray()
    {
        return $this->files;
    }

    /**
     * {@inheritdoc}
     */
    public function get($file, $asObj = true)
    {
        if ($file = Arr::get($this->all(), $file, '.')) {
            return $asObj ? $this->findFiles($file) : $file;
        }
    }

    /**
     * getFixed
     *
     * @return void
     */
    public function all()
    {
        if (null === $this->fixed) {
            $this->fixed = $this->fixFilesArray($this->files);
        }

        return $this->fixed;
    }

    /**
     * fixFilesArray
     *
     * @param array $data
     *
     * @return array
     */
    private function fixFilesArray(array $data)
    {
        $keys = array_keys($data);
        sort($keys);

        if ($keys !== self::$fileKeys || !isset($data['tmp_name']) || !is_array($data['tmp_name'])) {

            if (is_array($data)) {
                foreach ($data as $name => $value) {
                    $data[$name] = is_array($value) ? $this->fixFilesArray($value) : $value;
                }
            }

            return $data;
        }

        $files = $data;

        foreach (array_keys($data['name']) as $i => $key) {

            $file = [];

            foreach ($keys as $name) {
                unset($files[$name]);
                $file[$name] = $data[$name][$key];
            }

            $files[$key] = $this->fixFilesArray($file);
        }

        return $files;
    }

    /**
     * findFiles
     *
     * @param array $data
     *
     * @return void
     */
    private function findFiles(array $data)
    {
        $keys = array_keys($data);
        sort($keys);

        if (static::$fileKeys === $keys) {
            return new UploadedFile($data);
        }

        foreach ($data as &$item) {
            if (is_array($item)) {
                $item = $this->findFiles($item);
            }
        }

        return $data;
    }
}
