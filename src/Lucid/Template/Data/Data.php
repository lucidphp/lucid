<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Data;

use Lucid\Template\ViewManagerInterface;

/**
 * @class Parameters
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Data implements TemplateDataInterface
{
    /**
     * data
     *
     * @var array
     */
    private $data;

    /**
     * replace
     *
     * @var boolean
     */
    private $replace;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->data = [];
        $this->replace = false;
    }

    /**
     * get
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * all
     *
     * @param array $parameters
     *
     * @return array
     */
    public function all(array $parameters = [])
    {
        if ($this->replace) {
            return $this->data;
        }

        return array_merge($parameters, $this->data);
    }

    /**
     * set
     *
     * @param array $data
     *
     * @return void
     */
    public function set(array $data)
    {
        $this->data = [];
        $this->replace = false;

        foreach ($data as $key => $value) {
            $this->add($key, $value);
        }
    }

    /**
     * replace
     *
     * @param array $data
     *
     * @return void
     */
    public function replace(array $data)
    {
        $this->set($data);
        $this->replace = true;
    }

    /**
     * addData
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function add($key, $value)
    {
        if (is_numeric($key)) {
            throw new \InvalidArgumentException('Key must not be a number.');
        }

        $this->data[$key] = $value;
    }
}
