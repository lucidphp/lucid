<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Request;

/**
 * @class PassResponseMapper
 *
 * @package Lucid\Routing\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PassResponseMapper implements ResponseMapperInterface
{
    /**
     * {@inheritdoc}
     *
     * Will just pass the input data.
     *
     * @return mixed the input value.
     */
    public function mapResponse($response)
    {
        return $response;
    }
}
