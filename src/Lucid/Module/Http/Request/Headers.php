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

use Lucid\Module\Http\Parameters;
use Lucid\Module\Http\Traits\HeadersMutableTrait;

/**
 * @class Headers
 *
 * @package Lucid\Module\Http\Request
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
