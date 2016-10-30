<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Request;

/**
 * @class ResponseMapperInterface
 *
 * @package Lucid\Routing\Http
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
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
