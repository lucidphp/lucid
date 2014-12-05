<?php

/*
 * This File is part of the Lucid\Module\Routing\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Http;

/**
 * @class ResponseMapperInterface
 *
 * @package Lucid\Module\Routing\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResponseMapperInterface
{
    /**
     * Maps a given input to a response.
     *
     * @param mixed $response the input response data
     *
     * @return mixed depending on the implementation, typically a response
     * object.
     */
    public function mapResponse($response);
}
