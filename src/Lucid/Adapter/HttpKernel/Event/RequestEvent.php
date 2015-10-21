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
use Symfony\Component\HttpFoundation\Request;

/**
 * @class ResponseEvent
 *
 * @package Lucid\Adapter\HttpKernel\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RequestEvent extends ResponseEvent
{
    private $request;
    private $type;

    public function __construct(Request $request, $type)
    {
        $this->request = $request;
        $this->type = $type;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getType()
    {
        return $this->type;
    }
}
