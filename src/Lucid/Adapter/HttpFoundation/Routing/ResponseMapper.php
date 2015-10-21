<?php

/*
 * This File is part of the Lucid\Adapter\HttpFoundation\Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpFoundation\Routing;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Lucid\Routing\Http\ResponseMapperInterface;

/**
 * @class ResponseMapper
 *
 * @package Lucid\Adapter\HttpFoundation\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ResponseMapper implements ResponseMapperInterface
{
    /**
     * {@inheritdoc}
     */
    public function mapResponse($response)
    {
        if ($response instanceof Response) {
            return $response;
        }

        if (is_string($response)) {
            return new Response($response);
        }

        if (is_array($response)) {
            return new JsonResponse($response);
        }

        if (is_callable($response)) {
            return new StreamedResponse($response);
        }

        throw new \InvalidArgumentException;
    }
}
