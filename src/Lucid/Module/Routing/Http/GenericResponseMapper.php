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

use Symfony\Component\HttpFoundation\Response;

/**
 * @class GenericResponseMapper
 *
 * @package Lucid\Module\Routing\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class GenericResponseMapper implements ResponseMapperInterface
{
    public function mapResponse($response)
    {
        if ($response instanceof Response) {
            return $response;
        }

        return new Response($response);
    }
}
