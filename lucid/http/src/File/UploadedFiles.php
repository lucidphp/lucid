<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\File;

use Psr\Http\Message\UploadedFileInterface;

/**
 * @class UploadedFiles
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class UploadedFiles implements UploadedFilesInterface
{
    /** @var array */
    const FILE_KEYS = ['error', 'name', 'size', 'tmp_name', 'type'];

    /** @var bool */
    private $isFixed;

    /** @var array */
    private $raw;

    /** @var array */
    private $files;

    /**
     * Constructor.
     *
     * @param array $files an array of uploaded files, like $_FILES
     */
    public function __construct(array $files = [])
    {
        $this->raw = $files;
        $this->files = [];
        $this->isFixed = false;
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
    public function all()
    {
        if (!$this->isFixed) {
            $this->files = $this->fixFilesArray($this->raw);
            $this->isFixed = true;
        }

        return $this->files;
    }

    /**
     * {@inheritdoc}
     */
    public function raw()
    {
        return $this->raw;
    }

    /**
     * Fix raw files array
     *
     * @param array $data
     *
     * @return array
     */
    private function fixFilesArray(array $data)
    {
        if (!$this->dataIsFileStruct($data)) {
            foreach ($data as $name => $value) {
                $data[$name] = is_array($value) ? $this->fixFilesArray($value) : $value;
            }

            return $data;
        }

        if (is_array($data['name'])) {
            $files = $data;

            foreach ($data['name'] as $index => $value) {
                $file = [];
                foreach (self::FILE_KEYS as $name) {
                    unset($files[$name]);
                    $file[$name] = $data[$name][$index];
                }

                $files[$index] = $this->fixFilesArray($file);
            }

            return $files;
        }

        return new UploadedFile(
            new FileInfo($data['name'], $data['size'], $data['type'], $data['tmp_name'], $data['error'])
        );
    }

    /**
     * Check if input array has files keys.
     *
     * @param array $data
     *
     * @return bool
     */
    private function dataIsFileStruct(array $data)
    {
        $keys = array_keys($data);
        sort($keys);

        return self::FILE_KEYS === $keys;
    }

    /**
     * Deep getter for the files array
     *
     * @param string $path
     * @param array $input
     *
     * @return void
     */
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
