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

        if (isset($this->files[$file])) {
            return $this->files[$file];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        if (!$this->isFixed) {
            foreach ($data = $this->fixFilesArray($this->raw) as $file) {
                $uploadFile = $this->createFile($file);
                $this->files[$uploadFile->getClientFilename()] = $uploadFile;
            }

            $this->isFixed = true;
            $this->files = $this->fixFilesArray($this->files);
        }

        return array_values($this->files);
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

        if (!$this->dataIsFileStrut($data) || !isset($data['tmp_name']) || !is_array($data['tmp_name'])) {
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

            var_dump($files);

            $files[$key] = $this->fixFilesArray($file);
        }

        return $files;
    }

    /**
     * createFile
     *
     * @param array $data
     *
     * @return File
     */
    private function createFile(array $data)
    {
        if (!$this->dataIsFileStrut($data)) {
            foreach ($data as $key => $file) {
                return $this->createFile($file);
            }
        }

        return new File(
            new FileInfo($data['name'], $data['size'], $data['type'], $data['tmp_name'], $data['error'])
        );
    }

    public function dataIsFileStrut(array $data)
    {
        return 0 !== count(array_intersect(array_keys($data), static::$fileKeys));
    }
}
