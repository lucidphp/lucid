<?php

/*
 * This File is part of the Lucid\Module\Routing package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing;

use Lucid\Module\Routing\Http\RequestContextInterface;

/**
 * @interface RouterInterface
 *
 * @package Lucid\Module\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RouterInterface
{
    public function dispatch(RequestContextInterface $request);

    public function getCurrentRoute();
}
