<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Parser;

/**
 * @class Token
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Token implements TokenInterface
{
    /** @var array */
    private $params;

    /**
     * Constructor.
     *
     * @param array $params token values.
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * {@inheritdoc}
     */
    public function isVariable()
    {
        return self::T_VARIABLE === $this->params[0];
    }

    /**
     * {@inheritdoc}
     */
    public function isText()
    {
        return self::T_TEXT === $this->params[0];
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired()
    {
        return $this->isVariable() ? $this->params[4] : true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSeparator()
    {
        return $this->isVariable() ? $this->params[1] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getRegexp()
    {
        return $this->isVariable() ? $this->params[2] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->isText() ? $this->params[1] : $this->params[3];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->params[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->params[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->params[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->params[$offset]);
    }
}
