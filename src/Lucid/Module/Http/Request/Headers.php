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

use Lucid\Module\Http\ParameterMutableInterface;

/**
 * @class Headers
 *
 * @package Lucid\Module\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Headers implements ParameterMutableInterface
{
    private $headers;

    public function __construct(array $headers = [])
    {
        $this->setHeaders($headers);
    }

    /**
     * has
     *
     * @param string $header
     *
     * @return boolean
     */
    public function has($header)
    {
        return isset($this->headers[$header]);
    }

    /**
     * remove
     *
     * @param mixed $header
     *
     * @return void
     */
    public function remove($header)
    {
        unset($this->headers[$header]);
    }

    /**
     * get
     *
     * @param string $header
     * @param array $default
     *
     * @return array
     */
    public function get($header, $default = null)
    {
        return isset($this->headers[$header]) ? $this->header[$header] : $default;
    }

    /**
     * add
     *
     * @param string $header
     * @param string|array $value
     *
     * @return void
     */
    public function add($header, $value)
    {
        if (isset($this->headers[$header])) {
            $this->set($header, $value);

            return;
        }

        $this->headers[$header] = array_merge($this->get($header, []), $this->getValue($value));
    }

    /**
     * set
     *
     * @param string $header
     * @param string|array $value
     *
     * @return void
     */
    public function set($header, $value)
    {
        $this->headers[$header] = $this->getValue($value);
    }

    /**
     * all
     *
     * @return array
     */
    public function all()
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        return array_keys($this->headers);
    }

    /**
     * setHeaders
     *
     * @param array $headers
     *
     * @return void
     */
    protected function setHeaders(array $headers)
    {
        foreach ($headers as $header => $values) {
            $this->set($header, $values);
        }
    }

    /**
     * getValue
     *
     * @param mixed $value
     *
     * @return array
     */
    protected function getValue($value)
    {
        return is_string($value) ? explode(',', $value)  : array_values((array)$value);
    }
}
