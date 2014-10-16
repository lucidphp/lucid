<?php

/*
 * This File is part of the Lucid\Module\Routing\Matcher package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Matcher;

use Lucid\Module\Routing\Http\RequestContextInterface;

/**
 * @class RequestMatcherInterface
 *
 * @package Lucid\Module\Routing\Matcher
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface RequestMatcherInterface
{
    const MATCH = 200;

    const NOMATCH = 500;

    /**
     * matchRequest
     *
     * @param RequestContextInterface $context
     *
     * @return array
     */
    public function matchRequest(RequestContextInterface $context);
}
