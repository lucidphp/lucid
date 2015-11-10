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

use Lucid\Http\Parameters;
use Lucid\Http\Traits\HeadersMutableTrait;

/**
 * @class Headers
 *
 * @package Lucid\Http\Request
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Headers extends Parameters
{
    use HeadersMutableTrait;

    public function __construct(array $headers = [])
    {
        $this->setHeaders($headers);
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
        $this->parameters = [];

        foreach ($headers as $header => $values) {
            $this->setHeader($this->parameters, $header, $values);
        }
    }
}
