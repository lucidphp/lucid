<?php

/*
 * This File is part of the Lucid\Http\Request package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Request;

use Psr\Http\Message\UploadedFileInterface;

/**
 * @class Files
 *
 * @package Lucid\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Files implements UploadedFilesInterface
{
    /**
     * isFixed
     *
     * @var bool
     */
    private $isFixed;

    /**
     * files
     *
     * @var array
     */
    private $raw;

    /**
     * fixed
     *
     * @var files
     */
    private $files;

    /**
     * fileKeys
     *
     * @var array
     */
    private static $fileKeys = ['error', 'name', 'size', 'tmp_name', 'type'];

    /**
     * Constructor.
     *
     * @param array $files
     */
    public function __construct(array $files = [])
    {
        $this->raw = $files;
        $this->files = [];
        $this->isFixed = false;
    }

    public function setFiles(array $files)
    {
        foreach ($files as $file) {
            $this->add($file);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(UploadedFileInterface $file)
    {
        $this->fixed[$file->getClientFilename()] = $file;
    }

    /**
     * {@inheritdoc}
     */
    public function get($file)
    {
        if (!$this->isFixed) {
            $this->all();
        }

        return $this->doGet($file, $this->files);
    }

    /**
     * {@inheritdoc}
     */
    public function all($flat = false)
    {
        if (!$this->isFixed) {
            $this->isFixed = true;
            $this->files = $this->fixFilesArray($this->raw);
        }

        if (false !== $flat) {
            return $this->flattenFiles($this->files);
        }

        return $this->files;
    }

    private function flattenFiles(array $files, $path = '')
    {
        $out = [];

        foreach ($files as $key => $value) {
            if ($value instanceof \Psr\Http\Message\UpladedFileInterface) {
            }
            if (!is_array($value)) {
                $out[$key] = $value;
            } else {
                $out[trim($path . '/' .  $key, '/')] = $this->flattenFiles($value, $key);
            }
        }

        return $out;
    }

    /**
     * {@inheritdoc}
     */
    public function raw()
    {
        return $this->raw;
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

        if (!$this->dataIsFileStruct($data) || !isset($data['tmp_name']) || !is_array($data['tmp_name'])) {
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

            $files[$key] = new File(
                new FileInfo($file['name'], $file['size'], $file['type'], $file['tmp_name'], $file['error'])
            );
        }

        return $files;
    }

    /**
     * createFile
     *
     * @param array $data
     *
     * @return void
     */
    private function createFile(array &$data)
    {
        if (!$this->dataIsFileStruct($data)) {
            foreach ($data as $key => &$file) {
                $this->createFile($file);
            }

            return;
        }

        $data = new File(
            new FileInfo($data['name'], $data['size'], $data['type'], $data['tmp_name'], $data['error'])
        );
    }

    private function dataIsFileStruct(array $data)
    {
        return 0 !== count(array_intersect(array_keys($data), static::$fileKeys));
    }

    private function doGet($path, array $input)
    {
        $parts = explode('/', $path);
        while ($key = array_shift($parts)) {
            if (is_array($input) && array_key_exists($key, $input)) {
                $input = $input[$key];
            } else {
                return null;
            }
        }

        return $input;
    }
}
