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
    const PASS_EMPTY_RESULT = 0;
    const TRANS_EMPTY_RESULT = 1;

    public function dispatch(RequestContextInterface $request, $behavior = self::TRANS_EMPTY_RESULT);
}
