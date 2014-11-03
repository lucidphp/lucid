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

use Symfony\Component\HttpFoundation\Request;

/**
 * @class ExceptionEvent
 *
 * @package Lucid\Adapter\HttpKernel\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ExceptionEvent extends RequestEvent
{
    private $exception;

    public function __construct(\Exception $e, Request $request, $type)
    {
        $this->exception = $e;
        parent::__construct($request, $type);
    }

    public function getException()
    {
        return $this->exception;
    }
}
