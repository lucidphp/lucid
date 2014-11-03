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

/**
 * @class Events
 *
 * @package Lucid\Adapter\HttpKernel\Event
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class KernelEvents
{
    const ON_REQUEST   = 'kernel.on_request';

    const ON_RESPONSE  = 'kernel.on_response';

    const ON_EXCEPTION = 'kernel.on_exception';

    private function __construct()
    {
    }
}
