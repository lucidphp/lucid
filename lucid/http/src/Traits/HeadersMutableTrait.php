<?php

/*
 * This File is part of the Lucid\Http\Traits package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Traits;

/**
 * @trait HeaderMutableTrait
 *
 * @package Lucid\Http\Traits
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait HeadersMutableTrait
{
    /**
     * addHeader
     *
     * @param array $headers
     * @param mixed $header
     * @param mixed $values
     *
     * @return void
     */
    protected function addHeader(array $headers, $header, $values)
    {
        if (isset($headers[$header])) {
            $this->setHeader($headers, $header, $values);

            return;
        }

        $headers[$header] = array_merge($this->get($header, []), $this->getValue($values));
    }

    /**
     * setHeader
     *
     * @param mixed $headers
     * @param mixed $header
     * @param mixed $values
     *
     * @return void
     */
    protected function setHeader(array &$headers, $header, $values)
    {
        $headers[$header] = $this->getValue($values);
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
