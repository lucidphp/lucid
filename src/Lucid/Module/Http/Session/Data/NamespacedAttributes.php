<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http\Session\Data;

use Lucid\Module\Common\Helper\Arr;

/**
 * @class NamespacedAttributes
 * @see Attributes
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class NamespacedAttributes extends Attributes
{
    private $separator;

    /**
     * Constructor.
     *
     * @param array $attributes
     * @param string $separator
     */
    public function __construct($name, $key, array &$attributes = [], $separator = '.')
    {
        $this->separator = $separator;
        parent::__construct($name, $key, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        Arr::set($this->parameters, $key, $value, $this->getSeparator());
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (null !== ($val = Arr::get($this->parameters, $key, $this->getSeparator()))) {
            return $val;
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        return Arr::unsetKey($this->parameters, $key, $this->getSeparator());
    }

    /**
     * getSeparator
     *
     * @return string
     */
    protected function getSeparator()
    {
        return $this->separator;
    }
}
