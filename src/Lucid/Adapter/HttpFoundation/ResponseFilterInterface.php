<?php

/*
 * This File is part of the Lucid\Adapter\HttpFoundation package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpFoundation;

use Symfony\Component\HttpFoundation\Response;

/**
 * @class ResponseFilterInterface
 *
 * @package Lucid\Adapter\HttpFoundation
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResponseFilterInterface
{
    public function filter(Response $response);
}
