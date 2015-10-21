<?php

/*
 * This File is part of the Lucid\Adapter\HttpKernel\Event package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\HttpKernel\Event;

use Lucid\Event\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * @class ResponseEvent
 *
 * @package Lucid\Adapter\HttpKernel\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ResponseEvent extends Event
{
    private $response;

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
